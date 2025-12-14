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

                <!-- Current Shift Section -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #2c3e50; font-weight: 700;">
                        <i class="feather icon-send"></i>
                        من الوردية الحالية:
                    </h5>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label"><strong>كود الوردية:</strong></label>
                        <input type="text" class="form-control" value="{{ $currentShift->shift_code }}" readonly>
                        <input type="hidden" name="from_shift_id" value="{{ $currentShift->id }}">
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label"><strong>العامل:</strong></label>
                        <input type="text" class="form-control" value="{{ $currentShift->user->name }}" readonly>
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label"><strong>المسؤول:</strong></label>
                        <input type="text" class="form-control" value="{{ $currentShift->supervisor->name ?? 'لا يوجد' }}" readonly>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><strong>المرحلة:</strong></label>
                        <input type="text" class="form-control" value="المرحلة #{{ $stageNumber }}" readonly>
                        <input type="hidden" name="stage_number" value="{{ $stageNumber }}">
                        <input type="hidden" name="stage_record_id" value="{{ $stageRecordId }}">
                    </div>
                </div>

                <!-- Separator -->
                <div style="text-align: center; margin: 25px 0;">
                    <i class="feather icon-arrow-down" style="font-size: 24px; color: #667eea;"></i>
                </div>

                <!-- Target Shift Section -->
                <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 style="margin-bottom: 15px; color: #1b5e20; font-weight: 700;">
                        <i class="feather icon-receive"></i>
                        إلى الوردية المستهدفة:
                    </h5>

                    @if(count($availableShifts) > 0)
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><strong>اختر الوردية المستهدفة:</strong></label>
                        <select name="to_shift_id" class="form-control @error('to_shift_id') is-invalid @enderror" required>
                            <option value="">-- اختر وردية --</option>
                            @foreach ($availableShifts as $shift)
                            <option value="{{ $shift->id }}"
                                {{ old('to_shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->shift_code }} - {{ $shift->user->name }}
                                ({{ $shift->supervisor->name ?? 'لا يوجد مسؤول' }})
                            </option>
                            @endforeach
                        </select>
                        @error('to_shift_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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

                <!-- Barcode Field -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label"><strong>رمز الستاند (الباركود):</strong></label>
                    <input type="text" name="stage_record_barcode" class="form-control"
                        placeholder="يتم ملؤه تلقائياً إن وجد"
                        value="{{ old('stage_record_barcode') }}">
                    <small class="form-text text-muted">اختياري - سيتم استخدام الباركود الحالي إن لم يتم إدخاله</small>
                </div>

                <!-- Notes Field -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label"><strong>ملاحظات:</strong></label>
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="أضف ملاحظات حول نقل المرحلة (اختياري)"
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
                        • سيتم نقل المرحلة من الوردية الحالية إلى الوردية المختارة
                    </p>
                    <p style="color: #856404; margin-bottom: 5px;">
                        • سيتم تسجيل كافة العمال العاملين على هذه المرحلة كمنقولين
                    </p>
                    <p style="color: #856404; margin-bottom: 0;">
                        • لا يمكن التراجع عن هذه العملية إلا يدوياً
                    </p>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-secondary">
                        <i class="feather icon-x"></i> إلغاء
                    </a>
                    <button type="submit" class="um-btn um-btn-success">
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
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('🚀 Form Submission Started');
            console.log('📝 Form Data:');

            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }

            console.log('✅ Form is about to submit to:', form.action);
        });
    }
});
</script>

@endsection

