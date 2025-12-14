@extends('master')

@section('title', 'نقل المرحلة بين الورديات')

@section('content')

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <i class="feather icon-arrow-right-circle"></i>
            نقل المرحلة (الستاند) بين الورديات
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <a href="{{ route('manufacturing.shift-handovers.index') }}">نقل الورديات</a>
            <i class="feather icon-chevron-left"></i>
            <span>نقل المرحلة</span>
        </nav>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
    <div class="um-alert-custom um-alert-danger" role="alert" style="margin-bottom: 20px;">
        <i class="feather icon-alert-circle"></i>
        <strong>❌ خطأ في البيانات:</strong>
        <ul style="margin: 10px 0 0 20px;">
            @foreach ($errors->all() as $error)
            <li style="margin: 5px 0;"><strong>{{ $error }}</strong></li>
            @endforeach
        </ul>
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
    @endif

    <!-- Session Error -->
    @if (session('error'))
    <div class="um-alert-custom um-alert-danger" role="alert" style="margin-bottom: 20px;">
        <i class="feather icon-alert-circle"></i>
        <strong>❌ خطأ:</strong>
        <p style="margin: 10px 0 0 0; color: #721c24;">{{ session('error') }}</p>
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
    @endif

    <!-- Session Success -->
    @if (session('success'))
    <div class="um-alert-custom um-alert-success" role="alert" style="margin-bottom: 20px;">
        <i class="feather icon-check-circle"></i>
        <strong>✅ نجاح:</strong>
        <p style="margin: 10px 0 0 0; color: #155724;">{{ session('success') }}</p>
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
    @endif

    <!-- Main Form Card -->
    <section class="um-main-card">
        <div class="um-card-header">
            <h4 class="um-card-title">
                <i class="feather icon-layers"></i>
                معلومات نقل المرحلة
            </h4>
        </div>

        <div class="um-card-body">
            <form action="{{ route('manufacturing.shift-handovers.transfer-stage-store') }}" method="POST" class="form-horizontal">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" name="from_shift_id" value="{{ $currentShift->id }}">
                <input type="hidden" name="stage_number" value="{{ $stageNumber }}">
                <input type="hidden" name="stage_record_id" value="{{ $stageRecordId }}">

                <!-- Target Shift Selection Section -->
                <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #1b5e20; font-weight: 700;">
                        <i class="feather icon-arrow-right"></i>
                        اختر الوردية المستهدفة للنقل:
                    </h5>

                    @if(count($availableShifts) > 0)
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><strong>الوردية المستهدفة:</strong></label>
                        <select name="to_shift_id" id="toShiftSelect" class="form-control @error('to_shift_id') is-invalid @enderror" required>
                            <option value="">-- اختر وردية --</option>
                            @foreach ($shiftsWithWorkers as $shiftData)
                            <option value="{{ $shiftData['shift']->id }}"
                                {{ old('to_shift_id') == $shiftData['shift']->id ? 'selected' : '' }}>
                                {{ $shiftData['shift']->shift_code }} - {{ $shiftData['shift']->user->name }}
                                ({{ $shiftData['shift']->supervisor->name ?? 'لا يوجد مسؤول' }})
                            </option>
                            @endforeach
                        </select>
                        @error('to_shift_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- عرض العمال للوردية المختارة -->
                    <div id="shiftWorkersContainer" style="margin-top: 15px; display: none;">
                        <div style="padding: 15px; background: white; border-radius: 6px; border-right: 2px solid #27ae60;">
                            <label style="font-weight: 700; color: #2c3e50; display: block; margin-bottom: 10px;">
                                <i class="feather icon-users"></i> العمال في الوردية المختارة:
                            </label>
                            <div id="selectedShiftWorkers" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                                <!-- سيتم ملؤه ديناميكياً -->
                            </div>
                        </div>
                    </div>

                    @else
                    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; text-align: center;">
                        <i class="feather icon-alert-triangle" style="font-size: 24px; color: #f39c12; margin-bottom: 10px; display: block;"></i>
                        <p style="color: #856404; margin: 0; font-weight: 600;">
                            ❌ لا توجد ورديات نشطة متاحة للنقل إليها
                        </p>
                        <small style="color: #856404; display: block; margin-top: 8px;">
                            يجب أن تكون هناك ورديات بحالة "نشطة" قبل نقل المرحلة
                        </small>
                    </div>
                    @endif
                </div>

                <!-- Approval Modal Alert -->
                <div id="approvalAlert" style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: none;">
                    <h6 style="color: #e65100; margin-top: 0; margin-bottom: 10px; font-weight: 700;">
                        <i class="feather icon-alert-circle"></i>
                        تأكيد نقل المرحلة
                    </h6>
                    <p id="approvalMessage" style="color: #bf360c; margin-bottom: 15px; line-height: 1.6;">
                        تأكيد نقل المرحلة <strong id="stageNum"></strong> من الوردية <strong id="fromShiftCode"></strong>
                        إلى الوردية <strong id="toShiftCode"></strong>
                    </p>

                    <!-- Current Shift Details -->
                    <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                        <h6 style="color: #2c3e50; margin-top: 0; margin-bottom: 10px; font-weight: 700;">
                            <i class="feather icon-send"></i>
                            من الوردية الحالية:
                        </h6>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
                            <div>
                                <small style="color: #7f8c8d;">كود الوردية:</small>
                                <div style="font-weight: 600; color: #2c3e50;">{{ $currentShift->shift_code }}</div>
                            </div>
                            <div>
                                <small style="color: #7f8c8d;">العامل:</small>
                                <div style="font-weight: 600; color: #2c3e50;">{{ $currentShift->user->name }}</div>
                            </div>
                            <div>
                                <small style="color: #7f8c8d;">المسؤول:</small>
                                <div style="font-weight: 600; color: #2c3e50;">{{ $currentShift->supervisor->name ?? 'لا يوجد' }}</div>
                            </div>
                        </div>

                        <!-- عرض العمال في الوردية الحالية -->
                        @if($currentShiftWorkers && count($currentShiftWorkers) > 0)
                        <div style="margin-top: 10px; padding: 10px; background: #f0f7ff; border-radius: 6px; border-right: 2px solid #3498db;">
                            <small style="color: #1565c0; font-weight: 600;">العمال المسجلون ({{ count($currentShiftWorkers) }}):</small>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px; margin-top: 8px;">
                                @foreach($currentShiftWorkers as $worker)
                                <div style="background: white; padding: 8px; border-radius: 4px; border-left: 2px solid #27ae60; font-size: 12px;">
                                    <span style="background: #27ae60; color: white; width: 20px; height: 20px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-left: 5px;">
                                        <i class="feather icon-user" style="font-size: 10px;"></i>
                                    </span>
                                    <strong>{{ $worker->name }}</strong>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Approval Checkbox -->
                    <div style="background: #e8f5e9; border: 2px dashed #27ae60; padding: 15px; border-radius: 6px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="confirm_transfer" id="confirmTransfer" value="1"
                                style="width: 18px; height: 18px; margin-top: 2px; cursor: pointer;">
                            <div>
                                <div style="font-weight: 700; color: #1b5e20; margin-bottom: 3px;">
                                    أوافق على نقل هذه المرحلة
                                </div>
                                <small style="color: #2e7d32; line-height: 1.5;">
                                    أؤكد أنني أوافق على نقل المرحلة من الوردية الحالية إلى الوردية المختارة، وتحمل مسؤولية هذا النقل.
                                </small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes Field -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label"><strong>ملاحظات:</strong></label>
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="أضف ملاحظات حول نقل المرحلة (اختياري)"
                        maxlength="1000">{{ old('notes') }}</textarea>
                    <small class="form-text text-muted">الحد الأقصى: 1000 حرف</small>
                </div>                <!-- Form Actions -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-secondary">
                        <i class="feather icon-x"></i> إلغاء
                    </a>
                    <button type="submit" id="submitBtn" class="um-btn um-btn-success" disabled>
                        <i class="feather icon-check-circle"></i> تأكيد نقل المرحلة
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

.um-btn-success:hover {
    background-color: #229954;
}

.um-btn-success:disabled {
    background-color: #95a5a6;
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
}

.um-btn-success:disabled:hover {
    background-color: #95a5a6;
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
    // التحكم في عرض/إخفاء الموافقة والزر
    const toShiftSelect = document.getElementById('toShiftSelect');
    const approvalAlert = document.getElementById('approvalAlert');
    const confirmCheckbox = document.getElementById('confirmTransfer');
    const submitBtn = document.getElementById('submitBtn');

    // بيانات الورديات والعمال
    const shiftsWorkers = {
        @foreach($shiftsWithWorkers as $shiftData)
        '{{ $shiftData["shift"]->id }}': {
            workers: [
                @foreach($shiftData['workers'] as $worker)
                { id: {{ $worker->id }}, name: '{{ $worker->name }}' },
                @endforeach
            ],
            shift_code: '{{ $shiftData['shift']->shift_code }}'
        },
        @endforeach
    };

    // عند اختيار وردية مستهدفة
    if (toShiftSelect) {
        toShiftSelect.addEventListener('change', function() {
            const selectedShiftId = this.value;
            confirmCheckbox.checked = false; // إعادة تعيين الموافقة
            submitBtn.disabled = true;

            if (selectedShiftId) {
                // عرض بيانات الموافقة
                const shiftData = shiftsWorkers[selectedShiftId];
                if (shiftData) {
                    document.getElementById('stageNum').textContent = '{{ $stageNumber }}';
                    document.getElementById('fromShiftCode').textContent = '{{ $currentShift->shift_code }}';
                    document.getElementById('toShiftCode').textContent = shiftData.shift_code;

                    // عرض العمال للوردية المختارة
                    const workersList = document.getElementById('selectedShiftWorkers');
                    if (shiftData.workers.length > 0) {
                        workersList.innerHTML = shiftData.workers.map(worker => `
                            <div style="background: #e8f5e9; padding: 10px; border-radius: 6px; border-left: 2px solid #27ae60;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="background: #27ae60; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                        <i class="feather icon-user" style="font-size: 12px;"></i>
                                    </span>
                                    <strong style="color: #2c3e50;">${worker.name}</strong>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        workersList.innerHTML = `
                            <div style="padding: 12px; background: #fff3cd; border-radius: 6px; color: #856404; font-size: 13px; grid-column: 1/-1;">
                                <i class="feather icon-alert-circle"></i> لا توجد عمال مسجلين في هذه الوردية
                            </div>
                        `;
                    }
                    document.getElementById('shiftWorkersContainer').style.display = 'block';
                    approvalAlert.style.display = 'block';
                } else {
                    approvalAlert.style.display = 'none';
                    document.getElementById('shiftWorkersContainer').style.display = 'none';
                }
            } else {
                approvalAlert.style.display = 'none';
                document.getElementById('shiftWorkersContainer').style.display = 'none';
            }
        });

        // التحقق من الموافقة
        if (confirmCheckbox) {
            confirmCheckbox.addEventListener('change', function() {
                submitBtn.disabled = !this.checked;
            });
        }
    }

    // فحص القيمة المحفوظة عند تحميل الصفحة
    if (toShiftSelect && toShiftSelect.value) {
        toShiftSelect.dispatchEvent(new Event('change'));
    }

    // التحقق من النموذج قبل الإرسال
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!confirmCheckbox || !confirmCheckbox.checked) {
                e.preventDefault();
                alert('⚠️ يجب عليك الموافقة على نقل المرحلة أولاً');
                confirmCheckbox.focus();
                return false;
            }
        });
    }
});
</script>

@endsection

