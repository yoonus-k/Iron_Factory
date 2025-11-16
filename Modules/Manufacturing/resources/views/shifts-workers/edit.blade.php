@extends('master')

@section('title', 'تعديل بيانات الوردية')

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
                تعديل بيانات الوردية
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل وردية</span>
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
                            <h3 class="section-title">معلومات الوردية</h3>
                            <p class="section-subtitle">قم بتحديث البيانات الأساسية للوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="shift_code" class="form-label">
                                رقم الوردية
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
                                    value="{{ old('shift_code', $shift->shift_code) }}" placeholder="أدخل رقم الوردية" required readonly>
                            </div>
                            @error('shift_code')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shift_date" class="form-label">
                                تاريخ الوردية
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
                                نوع الوردية
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
                                    <option value="">اختر نوع الوردية</option>
                                    <option value="morning" {{ old('shift_type', $shift->shift_type) == 'morning' ? 'selected' : '' }}>الفترة الأولى (6 ص - 6 م)</option>
                                    <option value="evening" {{ old('shift_type', $shift->shift_type) == 'evening' ? 'selected' : '' }}>الفترة الثانية (6 م - 6 ص)</option>
                                </select>
                            </div>
                            @error('shift_type')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="supervisor_id" class="form-label">
                                المسؤول عن الوردية
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
                                    <option value="">اختر المسؤول</option>
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
                                وقت البدء
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
                                وقت الانتهاء
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

                        <input type="hidden" name="stage_number" value="0">

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">ملاحظات</label>
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
                                    class="form-input" placeholder="أدخل ملاحظات للوردية">{{ old('notes', $shift->notes) }}</textarea>
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
                            <h3 class="section-title">تعيين العمال</h3>
                            <p class="section-subtitle">حدد مجموعة عمال أو اختر العمال بشكل فردي</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- اختيار المجموعة -->
                        <div class="form-group full-width">
                            <label for="team_id" class="form-label">
                                اختر مجموعة عمال (اختياري)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <select name="team_id" id="team_id" class="form-input" onchange="loadTeamWorkers()">
                                    <option value="">اختر مجموعة (أو اختر العمال يدوياً)</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}" 
                                            data-workers-count="{{ $team->workers_count }}">
                                        {{ $team->name }} ({{ $team->workers_count }} عامل)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted">
                                <i class="feather icon-info"></i>
                                عند اختيار مجموعة، سيتم تحديد جميع العمال فيها تلقائياً
                            </small>
                        </div>

                        <div class="form-group full-width">
                            <div class="workers-selection-header">
                                <label class="form-label">العمال المعينون</label>
                                <div class="selection-info">
                                    <span class="selected-count">تم اختيار: <strong id="selectedWorkersCount">0</strong></span>
                                    <button type="button" class="btn-clear-selection" onclick="clearAllWorkers()">
                                        <i class="feather icon-x"></i> إلغاء الكل
                                    </button>
                                </div>
                            </div>
                            <div class="workers-selection">
                                @forelse($workers as $worker)
                                    <div class="worker-item">
                                        <input type="checkbox" 
                                               id="worker_{{ $worker->id }}" 
                                               name="workers[]" 
                                               value="{{ $worker->id }}"
                                               class="worker-checkbox"
                                               {{ in_array($worker->id, old('workers', $shift->worker_ids ?? [])) ? 'checked' : '' }}
                                               onchange="updateWorkersCount()">
                                        <label for="worker_{{ $worker->id }}">
                                            {{ $worker->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p style="color: #999;">لا يوجد عمال متاحون</p>
                                @endforelse
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
                            <h3 class="section-title">معلومات إضافية</h3>
                            <p class="section-subtitle">معلومات إضافية عن الوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="status" class="form-label">
                                حالة الوردية
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
                                تاريخ الإنشاء
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
                                تاريخ التحديث
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
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="btn-cancel">
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
                    alert(`تم تحديد ${workersCount} عامل من ${teamName}`);
                } else {
                    alert('المجموعة لا تحتوي على عمال');
                }
            } catch (error) {
                console.error('Error loading team workers:', error);
                alert('حدث خطأ في تحميل عمال المجموعة: ' + error.message);
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
        }

        // تحديث الأوقات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateShiftTimes();
            updateWorkersCount();
        });
    </script>

    <style>
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

@endsection