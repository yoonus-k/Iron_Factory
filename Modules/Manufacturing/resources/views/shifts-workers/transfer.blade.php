@extends('master')

@section('title', __('shifts-workers.transfer_shift'))

@section('content')

<style>
    :root {
        --primary-color: #0066B2;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    .transfer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .transfer-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #0052a3 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .transfer-header h1 {
        font-size: 28px;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .transfer-header p {
        margin: 5px 0;
        font-size: 14px;
        opacity: 0.95;
    }

    .transfer-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .shift-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .shift-card.before {
        border-right: 4px solid var(--warning-color);
    }

    .shift-card.after {
        border-right: 4px solid var(--success-color);
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title.before {
        color: var(--warning-color);
    }

    .card-title.after {
        color: var(--success-color);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6b7280;
        min-width: 150px;
    }

    .info-value {
        color: #1f2937;
        font-weight: 500;
        text-align: left;
    }

    .workers-list {
        background: #f9fafb;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .workers-list h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 12px 0;
        color: #374151;
    }

    .worker-badge {
        display: inline-block;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 6px 12px;
        margin: 4px;
        font-size: 13px;
        color: #1f2937;
    }

    .form-section {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
    }

    .form-section h2 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 102, 178, 0.1);
    }

    .workers-selection {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        max-height: 400px;
        overflow-y: auto;
    }

    .worker-item {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .worker-item:hover {
        background: #f9fafb;
    }

    .worker-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .worker-item label {
        margin: 0;
        cursor: pointer;
        flex: 1;
        font-size: 14px;
    }

    .selected-count {
        background: #f3f4f6;
        padding: 10px;
        border-radius: 6px;
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 12px;
    }

    .selected-count strong {
        color: var(--primary-color);
        font-size: 16px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-transfer {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
        color: white;
        flex: 1;
    }

    .btn-transfer:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-cancel {
        background: #e5e7eb;
        color: #374151;
        flex: 1;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-success {
        background: #d1fae5;
        border-left: 4px solid var(--success-color);
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        border-left: 4px solid var(--danger-color);
        color: #7f1d1d;
    }

    .icon {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .transfer-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<div class="transfer-container">
    <!-- Header -->
    <div class="transfer-header">
        <h1>
            <i class="feather icon-repeat"></i>
            نقل الوردية
        </h1>
        <p>
            <strong>الوردية:</strong> {{ $currentShift->shift_code }} |
            <strong>التاريخ:</strong> {{ $currentShift->shift_date->format('Y-m-d') }} |
            <strong>النوع:</strong> {{ $currentShift->shift_type == 'morning' ? 'الفترة الأولى' : 'الفترة الثانية' }}
        </p>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="feather icon-check-circle icon"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="feather icon-alert-circle icon"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Before and After Cards -->
    <div class="transfer-grid">
        <!-- Current/Before Card -->
        <div class="shift-card before">
            <div class="card-title before">
                <i class="feather icon-arrow-left"></i>
                البيانات الحالية
            </div>

            <div class="info-row">
                <span class="info-label">المسؤول:</span>
                <span class="info-value">{{ $supervisor ? $supervisor->name : 'لا يوجد' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">وقت البداية:</span>
                <span class="info-value">{{ $currentShift->start_time }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">وقت النهاية:</span>
                <span class="info-value">{{ $currentShift->end_time }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">المرحلة:</span>
                <span class="info-value">{{ $currentShift->stage_number ? 'المرحلة ' . $currentShift->stage_number : 'غير محددة' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">عدد العمال:</span>
                <span class="info-value">{{ count($workers) }}</span>
            </div>

            @if(count($workers) > 0)
                <div class="workers-list">
                    <h4>قائمة العمال:</h4>
                    @foreach($workers as $worker)
                        <span class="worker-badge">
                            {{ $worker->name }}
                            @if($worker->assigned_stage)
                                <br><small>(المرحلة: {{ $worker->assigned_stage }})</small>
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- New/After Card -->
        <div class="shift-card after">
            <div class="card-title after">
                <i class="feather icon-arrow-right"></i>
                البيانات الجديدة
            </div>

            <!-- New Supervisor -->
            <div class="form-group">
                <label for="new_supervisor_id">
                    <i class="feather icon-user"></i>
                    المسؤول الجديد
                </label>
                <select id="new_supervisor_id" name="new_supervisor_id" required>
                    <option value="">-- اختر المسؤول --</option>
                    @foreach($supervisors as $s)
                        <option value="{{ $s->id }}"
                            @if($s->id == $supervisor?->id) selected @endif>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
                @error('new_supervisor_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Workers Selection -->
            <div class="form-group">
                <label>
                    <i class="feather icon-users"></i>
                    العمال الجدد
                </label>
                <div class="selected-count">
                    تم اختيار <strong id="selectedCount">0</strong> عامل
                </div>
                <div class="workers-selection" id="workersContainer">
                    @foreach($allWorkers as $worker)
                        <div class="worker-item">
                            <input type="checkbox" class="worker-checkbox"
                                id="worker_{{ $worker->id }}"
                                value="{{ $worker->id }}"
                                @if(in_array($worker->id, $currentShift->worker_ids ?? []))
                                    checked
                                @endif>
                            <label for="worker_{{ $worker->id }}">
                                {{ $worker->name }}
                                @if($worker->assigned_stage)
                                    <small class="text-muted">(المرحلة: {{ $worker->assigned_stage }})</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="form-section">
        <h2>ملاحظات النقل</h2>

        <form method="POST" action="{{ route('manufacturing.shifts-workers.transfer-store', $currentShift->id) }}" id="finalForm">
            @csrf

            <div class="form-group">
                <label for="transfer_notes">أضف ملاحظات (اختياري):</label>
                <textarea id="transfer_notes" name="transfer_notes" rows="4"
                    placeholder="أكتب أي ملاحظات عن نقل الوردية..."></textarea>
            </div>

            <!-- New Supervisor Hidden Input -->
            <input type="hidden" id="hidden_supervisor" name="new_supervisor_id">

            <!-- Workers will be added dynamically here -->
            <div id="hidden_workers_container"></div>

            <div class="form-actions">
                <button type="button" class="btn btn-transfer" onclick="submitTransfer()">
                    <i class="feather icon-check"></i>
                    تأكيد النقل
                </button>
                <a href="{{ route('manufacturing.shifts-workers.show', $currentShift->id) }}" class="btn btn-cancel">
                    <i class="feather icon-x"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // تحديث عدد العمال المختارين
    document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll('.worker-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    // تحديث العد عند تحميل الصفحة
    updateSelectedCount();

    function submitTransfer() {
        const supervisorId = document.getElementById('new_supervisor_id').value;

        if (!supervisorId) {
            alert('يرجى اختيار المسؤول الجديد');
            return;
        }

        // جمع العمال المختارين
        const selectedWorkers = Array.from(document.querySelectorAll('.worker-checkbox:checked'))
            .map(checkbox => checkbox.value);

        console.log('Supervisor ID:', supervisorId);
        console.log('Selected Workers:', selectedWorkers);

        // إضافة المسؤول
        document.getElementById('hidden_supervisor').value = supervisorId;

        // إضافة العمال كـ hidden inputs
        const container = document.getElementById('hidden_workers_container');
        container.innerHTML = '';

        selectedWorkers.forEach(workerId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'new_workers[]';
            input.value = workerId;
            container.appendChild(input);
            console.log('Added worker:', workerId);
        });

        // طباعة ملخص البيانات
        console.log('Form Data Summary:');
        console.log('Supervisor ID:', document.getElementById('hidden_supervisor').value);
        console.log('Workers Count:', container.querySelectorAll('input').length);

        // إرسال النموذج
        document.getElementById('finalForm').submit();
    }
</script>

@endsection
