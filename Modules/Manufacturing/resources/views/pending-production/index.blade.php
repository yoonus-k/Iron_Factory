@extends('master')

@section('title', 'السجلات غير المكتملة')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-white">
                            <h3 class="mb-2 fw-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-hourglass-split me-2" viewBox="0 0 16 16">
                                    <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z"/>
                                </svg>
                                السجلات غير المكتملة
                            </h3>
                            <p class="mb-0 opacity-75">عرض وإدارة السجلات التي لم يتم إكمالها</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- فلاتر البحث --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('manufacturing.pending-production.index') }}">
                <div class="row g-3">
                    {{-- باركود --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">الباركود</label>
                        <input type="text" 
                               name="barcode" 
                               class="form-control" 
                               placeholder="ابحث بالباركود"
                               value="{{ request('barcode') }}">
                    </div>

                    {{-- المرحلة --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">المرحلة</label>
                        <select name="stage" class="form-select">
                            <option value="">جميع المراحل</option>
                            @foreach($stages as $key => $value)
                                <option value="{{ $key }}" {{ request('stage') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- العامل --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">العامل</label>
                        <select name="worker_id" class="form-select">
                            <option value="">جميع العمال</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- التاريخ من --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">من تاريخ</label>
                        <input type="date" 
                               name="date_from" 
                               class="form-control"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- التاريخ إلى --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">إلى تاريخ</label>
                        <input type="date" 
                               name="date_to" 
                               class="form-control"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- أزرار البحث --}}
                    <div class="col-md-9">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.pending-production.index') }}" class="btn btn-secondary px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise me-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                </svg>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- الجدول --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">#</th>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">الباركود</th>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">المرحلة</th>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">العامل</th>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">تاريخ البدء</th>
                            <th class="px-2 py-2" style="font-size: 12px; white-space: nowrap;">المدة</th>
                            <th class="px-2 py-2 text-center" style="font-size: 12px; white-space: nowrap;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td class="px-2 py-2" style="font-size: 12px;">{{ $records->firstItem() + $loop->index }}</td>
                                <td class="px-2 py-2">
                                    <strong class="text-primary" style="font-size: 12px;">{{ $record->barcode }}</strong>
                                </td>
                                <td class="px-2 py-2">
                                    @php
                                        $stageNames = [
                                            'warehouse' => 'مستودع',
                                            'stage1_stands' => 'مرحلة 1',
                                            'stage2_processed' => 'مرحلة 2',
                                            'stage3_coils' => 'مرحلة 3',
                                            'stage4_boxes' => 'مرحلة 4',
                                        ];
                                    @endphp
                                    <span class="badge bg-info" style="font-size: 10px; white-space: nowrap;">{{ $stageNames[$record->stage_type] ?? $record->stage_type }}</span>
                                </td>
                                <td class="px-2 py-2" style="max-width: 150px;">
                                    @if($record->worker_name)
                                        <div style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $record->worker_name }}">
                                            {{ $record->worker_name }}
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 11px;">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2" style="font-size: 11px; white-space: nowrap;">{{ \Carbon\Carbon::parse($record->started_at)->format('Y-m-d H:i') }}</td>
                                <td class="px-2 py-2">
                                    @php
                                        $duration = \Carbon\Carbon::parse($record->started_at)->diffForHumans();
                                    @endphp
                                    <span class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $duration }}</span>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-primary btn-sm"
                                                onclick="openReassignModal({{ $record->id }}, '{{ $record->barcode }}', '{{ $record->worker_name ?? 'غير محدد' }}')" 
                                                style="padding: 3px 8px; font-size: 11px;" 
                                                title="إسناد">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                            </svg>
                                            <span style="margin-right: 3px;">إسناد</span>
                                        </button>
                                        <a href="{{ route('manufacturing.pending-production.history', $record->barcode) }}" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           style="padding: 3px 8px; font-size: 11px;" 
                                           title="السجل">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                                                <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                                                <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox text-muted mb-3" viewBox="0 0 16 16">
                                        <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438L14.933 9zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374l3.7-4.625z"/>
                                    </svg>
                                    <p class="text-muted mb-0">لا توجد سجلات غير مكتملة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($records->hasPages())
            <div class="card-footer">
                {{ $records->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- مودال إعادة الإسناد --}}
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left-right me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                    إعادة إسناد السجل
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reassignForm">
                <div class="modal-body">
                    <input type="hidden" id="record_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الباركود</label>
                        <input type="text" id="barcode_display" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">العامل الحالي</label>
                        <input type="text" id="current_worker" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">العامل الجديد <span class="text-danger">*</span></label>
                        <select name="new_worker_id" id="new_worker_id" class="form-select" required>
                            <option value="">اختر العامل</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="shift_transfer" name="shift_transfer">
                            <label class="form-check-label fw-semibold" for="shift_transfer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
                                    <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                    <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                                </svg>
                                نقل للوردية التالية
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                ✓ فعّل هذا الخيار إذا كنت تريد نقل العمل للوردية التالية
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">سبب إعادة الإسناد</label>
                        <textarea name="reason" id="reason" class="form-control" rows="2" placeholder="اختياري"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="اختياري"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" id="saveReassignBtn" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg me-1" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                        </svg>
                        حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// جعل الدالة في الـ global scope
window.openReassignModal = function(recordId, barcode, currentWorker) {
    console.log('=== openReassignModal Called ===');
    console.log('Record ID:', recordId);
    console.log('Barcode:', barcode);
    console.log('Current Worker:', currentWorker);
    
    // التحقق من وجود العناصر
    const recordIdEl = document.getElementById('record_id');
    const barcodeEl = document.getElementById('barcode_display');
    const currentWorkerEl = document.getElementById('current_worker');
    const modalEl = document.getElementById('reassignModal');
    
    if (!recordIdEl || !barcodeEl || !currentWorkerEl || !modalEl) {
        console.error('ERROR: One or more elements not found!');
        console.error('record_id:', recordIdEl);
        console.error('barcode_display:', barcodeEl);
        console.error('current_worker:', currentWorkerEl);
        console.error('reassignModal:', modalEl);
        alert('خطأ: لم يتم العثور على النموذج المطلوب');
        return;
    }
    
    // ملء القيم
    recordIdEl.value = recordId;
    barcodeEl.value = barcode;
    currentWorkerEl.value = currentWorker;
    document.getElementById('new_worker_id').value = '';
    document.getElementById('reason').value = '';
    document.getElementById('notes').value = '';
    
    // التحقق من Bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('ERROR: Bootstrap is not loaded!');
        alert('خطأ: Bootstrap غير محمل');
        return;
    }
    
    console.log('Opening modal...');
    try {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        console.log('Modal shown successfully');
    } catch (error) {
        console.error('Error opening modal:', error);
        alert('خطأ في فتح النافذة: ' + error.message);
    }
};

// Save button click event
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Setting up button listener');
    
    const saveBtn = document.getElementById('saveReassignBtn');
    if (!saveBtn) {
        console.error('ERROR: saveReassignBtn not found!');
        return;
    }
    
    console.log('Button found, attaching click listener');
    
    saveBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('=== Save Button Clicked ===');
    
    const recordId = document.getElementById('record_id').value;
    const formData = {
        new_worker_id: document.getElementById('new_worker_id').value,
        reason: document.getElementById('reason').value,
        notes: document.getElementById('notes').value,
        shift_transfer: document.getElementById('shift_transfer').checked
    };
    
    console.log('Record ID:', recordId);
    console.log('Form Data:', formData);
    
    if (!recordId) {
        console.error('ERROR: No record ID!');
        alert('خطأ: لم يتم تحديد السجل');
        return;
    }
    
    if (!formData.new_worker_id) {
        console.error('ERROR: No new worker selected!');
        alert('الرجاء اختيار العامل الجديد');
        return;
    }
    
    const url = `{{ route('manufacturing.pending-production.reassign', ['id' => '__ID__']) }}`.replace('__ID__', recordId);
    console.log('Sending POST request to:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: data.message,
                confirmButtonText: 'حسناً'
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء إعادة الإسناد: ' + error.message,
            confirmButtonText: 'حسناً'
        });
    });
});
});
</script>
@endpush
