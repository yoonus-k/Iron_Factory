@extends('master')

@section('title', 'إضافة وردية جديدة')

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

            @if($errors->any())
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
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
                إضافة وردية جديدة
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>إضافة وردية جديدة</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.shifts-workers.store') }}" id="shiftForm">
                @csrf

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
                            <p class="section-subtitle">أدخل البيانات الأساسية للوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="shift_code" class="form-label">
                                رقم الوردية
                                <span class="required">*</span>
                            </label>
                            <div class="input-group-with-button">
                                <div class="input-wrapper" style="flex: 1;">
                                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input type="text" name="shift_code" id="shift_code"
                                        class="form-input"
                                        value="{{ old('shift_code') }}" placeholder="سيتم التوليد تلقائياً" required readonly>
                                </div>
                                <button type="button" class="btn-generate" onclick="generateShiftCode()" id="generateShiftBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <polyline points="1 20 1 14 7 14"></polyline>
                                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                    </svg>
                                    توليد 
                                </button>
                            </div>
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
                                    value="{{ old('shift_date', date('Y-m-d')) }}" required onchange="updateShiftCode()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shift_type" class="form-label">
                                فترة العمل
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <select name="shift_type" id="shift_type"
                                    class="form-input" required onchange="updateShiftCode(); updateShiftTimes()">
                                    <option value="">اختر فترة العمل</option>
                                    <option value="morning" {{ old('shift_type') == 'morning' ? 'selected' : '' }}>الفترة الأولى (6 صباحاً - 6 مساءً)</option>
                                    <option value="evening" {{ old('shift_type') == 'evening' ? 'selected' : '' }}>الفترة الثانية (6 مساءً - 6 صباحاً)</option>
                                </select>
                            </div>
                            <small class="text-muted">كل فترة 12 ساعة لتغطية العمل على مدار 24 ساعة</small>
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
                                    <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- المرحلة مرتبطة بالعامل وليس بالوردية -->
                        <input type="hidden" name="stage_number" value="0">

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
                                    class="form-input" readonly
                                    value="{{ old('start_time', '06:00') }}" required>
                            </div>
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
                                    class="form-input" readonly
                                    value="{{ old('end_time', '18:00') }}" required>
                            </div>
                        </div>

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
                                    class="form-input" placeholder="أدخل ملاحظات للوردية">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <div class="switch-group">
                                <input type="checkbox" id="is_active" name="is_active" value="1" class="switch-input" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        تفعيل الوردية
                                    </span>
                                </label>
                            </div>
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
                                            data-workers-count="{{ $team->workers_count }}"
                                            {{ old('team_id') == $team->id ? 'selected' : '' }}>
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
                                <label class="form-label">العمال المتاحون</label>
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
                                           {{ in_array($worker->id, old('workers', [])) ? 'checked' : '' }}
                                           onchange="updateWorkersCount()">
                                    <label for="worker_{{ $worker->id }}">{{ $worker->name }}</label>
                                </div>
                                @empty
                                <p style="color: #999; text-align: center; padding: 20px;">لا يوجد عمال متاحون حالياً</p>
                                <a href="{{ route('manufacturing.workers.create') }}" class="btn-submit" style="display: inline-block; margin-top: 10px;">
                                    <i class="feather icon-plus"></i>
                                    إضافة عامل جديد
                                </a>
                                @endforelse
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
                        حفظ الوردية
                    </button>
                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </a>
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

        .workers-selection-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .selection-info {
            width: 100%;
            justify-content: space-between;
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
// Auto-generate shift code on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize shift times
    updateShiftTimes();
    
    // Auto-generate code if date and type are set
    const date = document.getElementById('shift_date').value;
    const type = document.getElementById('shift_type').value;
    if (date && type) {
        generateShiftCode();
    }

    // Update workers count on page load
    updateWorkersCount();
});

// Generate shift code based on date and type
function generateShiftCode() {
    const date = document.getElementById('shift_date').value;
    const type = document.getElementById('shift_type').value;
    const btn = document.getElementById('generateShiftBtn');
    const codeInput = document.getElementById('shift_code');
    
    if (!date) {
        alert('الرجاء اختيار تاريخ الوردية أولاً');
        document.getElementById('shift_date').focus();
        return;
    }
    
    if (!type) {
        alert('الرجاء اختيار فترة العمل أولاً');
        document.getElementById('shift_type').focus();
        return;
    }

    // Add loading state
    btn.disabled = true;
    btn.classList.add('loading');
    const originalText = btn.innerHTML;
    btn.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"></polyline>
            <polyline points="1 20 1 14 7 14"></polyline>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
        </svg>
        جاري التوليد...
    `;
    
    fetch(`{{ route('manufacturing.shifts-workers.generate-code') }}?date=${date}&type=${type}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('فشل في توليد الكود');
            }
            return response.json();
        })
        .then(data => {
            codeInput.value = data.shift_code;
            
            // Success state
            btn.classList.remove('loading');
            btn.classList.add('success');
            btn.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                تم التوليد بنجاح
            `;
            
            // Reset after 2 seconds
            setTimeout(() => {
                btn.classList.remove('success');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Error state
            btn.classList.remove('loading');
            btn.classList.add('error');
            btn.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                فشل التوليد
            `;
            
            alert('حدث خطأ في توليد الكود. الرجاء المحاولة مرة أخرى.');
            
            // Reset after 2 seconds
            setTimeout(() => {
                btn.classList.remove('error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        });
}

// Update shift code when date or type changes
function updateShiftCode() {
    const date = document.getElementById('shift_date').value;
    const type = document.getElementById('shift_type').value;
    if (date && type) {
        generateShiftCode();
    }
}

// Update shift times based on shift type
function updateShiftTimes() {
    const shiftType = document.getElementById('shift_type').value;
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    
    if (shiftType === 'morning') {
        startTime.value = '06:00';
        endTime.value = '18:00';
    } else if (shiftType === 'evening') {
        startTime.value = '18:00';
        endTime.value = '06:00';
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
</script>

@endsection