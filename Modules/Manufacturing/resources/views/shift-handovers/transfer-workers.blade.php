@extends('master')

@section('title', 'نقل العمال بين الورديات')

@section('content')

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <i class="feather icon-users"></i>
            نقل العمال بين الورديات
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <a href="{{ route('manufacturing.shift-handovers.index') }}">نقل الورديات</a>
            <i class="feather icon-chevron-left"></i>
            <span>نقل العمال</span>
        </nav>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
    <div class="um-alert-custom um-alert-danger" role="alert">
        <i class="feather icon-alert-circle"></i>
        <strong>خطأ في البيانات:</strong>
        <ul style="margin: 10px 0 0 20px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
    @endif

    <!-- Main Form Card -->
    <section class="um-main-card">
        <div class="um-card-header">
            <h4 class="um-card-title">
                <i class="feather icon-arrow-right-circle"></i>
                نقل العمال النشطين
            </h4>
        </div>

        <div class="um-card-body">
            <form action="{{ route('manufacturing.shift-handovers.transfer-workers-store') }}" method="POST" class="form-horizontal">
                @csrf

                <!-- From Shift Section -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #2c3e50; font-weight: 700;">
                        <i class="feather icon-send"></i>
                        من الوردية:
                    </h5>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label"><strong>كود الوردية:</strong></label>
                        <input type="text" class="form-control" value="{{ $fromShift->shift_code }}" readonly>
                        <input type="hidden" name="from_shift_id" value="{{ $fromShift->id }}">
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label"><strong>العامل:</strong></label>
                        <input type="text" class="form-control" value="{{ $fromShift->user->name }}" readonly>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><strong>المسؤول:</strong></label>
                        <input type="text" class="form-control" value="{{ $fromShift->supervisor->name ?? 'لا يوجد' }}" readonly>
                    </div>
                </div>

                <!-- Workers List Section -->
                <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #1565c0; font-weight: 700;">
                        <i class="feather icon-list"></i>
                        اختر العمال للنقل:
                    </h5>

                    <div id="workers-list" style="max-height: 350px; overflow-y: auto;">
                        @if ($activeWorkers && count($activeWorkers) > 0)
                            @foreach ($activeWorkers as $worker)
                            <div style="background: white; padding: 12px; margin-bottom: 10px; border-radius: 6px; border-left: 3px solid #667eea;">
                                <label style="display: flex; align-items: center; gap: 12px; margin: 0; cursor: pointer;">
                                    <input type="checkbox" name="worker_ids[]" value="{{ $worker->worker_id }}"
                                        class="worker-checkbox"
                                        {{ in_array($worker->worker_id, old('worker_ids', [])) ? 'checked' : '' }}>
                                    <div style="flex: 1;">
                                        <strong style="display: block; color: #2c3e50;">
                                            @if($worker->worker_type === 'team' && $worker->team_name)
                                                👥 {{ $worker->team_name }}
                                            @else
                                                👤 {{ $worker->worker_name ?? 'غير محدد' }}
                                            @endif
                                        </strong>

                                        <small style="color: #3498db; display: block; margin-top: 4px;">
                                            ⏱️ بدء العمل: {{ \Carbon\Carbon::parse($worker->started_at)->format('H:i') }}
                                            ({{ \Carbon\Carbon::parse($worker->started_at)->diffForHumans(now(), ['syntax' => 'short']) }})
                                        </small>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        @else
                            <div style="text-align: center; padding: 30px; color: #7f8c8d;">
                                <i class="feather icon-info" style="font-size: 32px; margin-bottom: 10px;"></i>
                                <p>لا توجد عمال نشطين حالياً في هذه الوردية</p>
                            </div>
                        @endif
                    </div>

                    <!-- Select All Checkbox -->
                    @if ($activeWorkers && count($activeWorkers) > 0)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #bbb;">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                            <input type="checkbox" id="select-all-workers"
                                {{ count(old('worker_ids', [])) == count($activeWorkers) ? 'checked' : '' }}>
                            <strong>تحديد/إلغاء تحديد الكل</strong>
                        </label>
                    </div>
                    @endif

                    <!-- Count Badge -->
                    <div style="margin-top: 15px; text-align: center;">
                        <span style="display: inline-block; background: #667eea; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700;">
                            <i class="feather icon-check-square"></i>
                            تم تحديد: <span id="selected-count">0</span> من {{ count($activeWorkers) }}
                        </span>
                    </div>
                </div>

                <!-- Separator -->
                <div style="text-align: center; margin: 25px 0;">
                    <i class="feather icon-arrow-down" style="font-size: 24px; color: #667eea;"></i>
                </div>

                <!-- To Shift Section -->
                <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #1b5e20; font-weight: 700;">
                        <i class="feather icon-receive"></i>
                        إلى الوردية:
                    </h5>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><strong>اختر الوردية المستهدفة:</strong></label>
                        <input type="hidden" name="to_shift_id" id="to_shift_id_hidden">
                        <select id="to_shift_id_select" class="form-control @error('to_shift_id') is-invalid @enderror" required>
                            <option value="">-- جاري تحميل الورديات المتاحة --</option>
                        </select>
                        @error('to_shift_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div id="no-shifts-alert" style="display: none; margin-top: 10px; background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 6px;">
                            <i class="feather icon-alert-triangle" style="color: #f39c12;"></i>
                            <strong style="color: #856404;"> لا توجد ورديات نشطة متاحة</strong>
                            <p style="color: #856404; margin: 5px 0 0 0; font-size: 13px;">
                                يجب أن تكون هناك ورديات بحالة "نشطة" قبل نقل العمال
                            </p>
                        </div>
                    </div>

                    <div id="to-shift-details" style="margin-top: 15px; display: none; background: white; padding: 12px; border-radius: 6px;">
                        <div style="margin-bottom: 8px;">
                            <strong>العامل:</strong> <span id="to-worker-name"></span>
                        </div>
                        <div>
                            <strong>المسؤول:</strong> <span id="to-supervisor-name"></span>
                        </div>
                    </div>
                </div>

                <!-- Notes Field -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label"><strong>ملاحظات:</strong></label>
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="أضف ملاحظات حول نقل العمال (اختياري)"
                        maxlength="1000">{{ old('notes') }}</textarea>
                    <small class="form-text text-muted">الحد الأقصى: 1000 حرف</small>
                </div>

                <!-- Alert Box -->
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <h6 style="color: #856404; margin-bottom: 8px; font-weight: 700;">
                        <i class="feather icon-alert-triangle"></i>
                        تحذير مهم
                    </h6>
                    <p style="color: #856404; margin-bottom: 5px;">
                        • سيتم نقل العمال المختارين إلى الوردية الجديدة فوراً
                    </p>
                    <p style="color: #856404; margin-bottom: 5px;">
                        • سيستمر العمال في عملهم على نفس المرحلة ولكن مع الوردية الجديدة
                    </p>
                    <p style="color: #856404; margin-bottom: 0;">
                        • سيتم تسجيل هذا النقل في سجل العمليات
                    </p>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-secondary">
                        <i class="feather icon-x"></i> إلغاء
                    </a>
                    <button type="submit" class="um-btn um-btn-success" id="submit-btn" disabled>
                        <i class="feather icon-check-circle"></i> تأكيد نقل العمال
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>

<style>
.form-group label {
    margin-bottom: 8px;
    color: #2c3e50;
}

.form-control {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:disabled,
.form-control[readonly] {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

textarea.form-control {
    resize: vertical;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

select.form-control {
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: left 10px center;
    padding-left: 35px;
    appearance: none;
}

input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #667eea;
}

.um-btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.um-btn-success {
    background-color: #27ae60;
    color: white;
}

.um-btn-success:hover:not(:disabled) {
    background-color: #229954;
}

.um-btn-success:disabled {
    background-color: #bdc3c7;
    cursor: not-allowed;
    opacity: 0.7;
}

.um-btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.um-btn-secondary:hover {
    background-color: #7f8c8d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromShiftId = '{{ $fromShift->id }}';
    const stageNumber = '{{ $fromShift->stage_number }}';
    const toShiftSelect = document.getElementById('to_shift_id_select');
    const toShiftIdHidden = document.getElementById('to_shift_id_hidden');
    const selectAllCheckbox = document.getElementById('select-all-workers');
    const workerCheckboxes = document.querySelectorAll('.worker-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const submitBtn = document.getElementById('submit-btn');
    const toShiftDetails = document.getElementById('to-shift-details');
    const toWorkerName = document.getElementById('to-worker-name');
    const toSupervisorName = document.getElementById('to-supervisor-name');
    const noShiftsAlert = document.getElementById('no-shifts-alert');

    // تحميل الورديات المتاحة - جلب من نفس المرحلة
    fetch(`{{ route('manufacturing.shift-handovers.api.available-shifts') }}?stage_number=${stageNumber}&exclude_shift_id=${fromShiftId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // إزالة الخيار الافتراضي وإضافة الورديات
                toShiftSelect.innerHTML = '<option value="">-- اختر وردية --</option>';
                data.data.forEach(shift => {
                    const option = document.createElement('option');
                    option.value = shift.id;
                    option.textContent = `${shift.shift_code} - ${shift.worker_name}`;
                    option.dataset.supervisorName = shift.supervisor_name;
                    toShiftSelect.appendChild(option);
                });
                noShiftsAlert.style.display = 'none';
            } else {
                // لا توجد ورديات متاحة
                toShiftSelect.innerHTML = '<option value="" disabled selected>-- لا توجد ورديات متاحة --</option>';
                toShiftSelect.disabled = true;
                noShiftsAlert.style.display = 'block';
                submitBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading shifts:', error);
            noShiftsAlert.style.display = 'block';
        });

    // تحديث تفاصيل الوردية المختارة
    toShiftSelect.addEventListener('change', function() {
        toShiftIdHidden.value = this.value;
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            toWorkerName.textContent = selectedOption.textContent.split(' - ')[1] || '';
            toSupervisorName.textContent = selectedOption.dataset.supervisorName || 'لا يوجد';
            toShiftDetails.style.display = 'block';
        } else {
            toShiftDetails.style.display = 'none';
        }

        updateSubmitButton();
    });

    // تحديث عدد العمال المختارين
    function updateSelectedCount() {
        const selectedCount = Array.from(workerCheckboxes).filter(cb => cb.checked).length;
        selectedCountSpan.textContent = selectedCount;
    }

    function updateSubmitButton() {
        const selectedCount = Array.from(workerCheckboxes).filter(cb => cb.checked).length;
        const toShiftSelected = toShiftIdHidden.value !== '';
        submitBtn.disabled = selectedCount === 0 || !toShiftSelected;
    }

    // تحديد/إلغاء تحديد الكل
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            workerCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
            updateSubmitButton();
        });
    }

    // تحديث العد عند تغيير كل checkbox
    workerCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount();
            updateSubmitButton();

            // تحديث حالة "تحديد الكل"
            if (selectAllCheckbox) {
                const allChecked = Array.from(workerCheckboxes).every(c => c.checked);
                const someChecked = Array.from(workerCheckboxes).some(c => c.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // تحديث أولي
    updateSelectedCount();
    updateSubmitButton();
});
</script>

@endsection
