@extends('master')

@section('title', 'سجل التعيينات - ' . $barcode)

@section('content')
<div class="container-fluid py-4">
    {{-- العنوان --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-white">
                            <h3 class="mb-2 fw-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-clock-history me-2" viewBox="0 0 16 16">
                                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                                </svg>
                                سجل التعيينات
                            </h3>
                            <p class="mb-0 opacity-75">الباركود: <strong>{{ $barcode }}</strong></p>
                        </div>
                        <a href="{{ route('manufacturing.pending-production.index') }}" class="btn btn-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                            </svg>
                            رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline التعيينات --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($logs->isEmpty())
                        <div class="text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox text-muted mb-3" viewBox="0 0 16 16">
                                <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438L14.933 9zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374l3.7-4.625z"/>
                            </svg>
                            <p class="text-muted mb-0">لا توجد سجلات تعيين لهذا الباركود</p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($logs as $log)
                                @php
                                    $stageNames = [
                                        'warehouse' => 'المستودع',
                                        'stage1_stands' => 'المرحلة الأولى - الاستاند',
                                        'stage2_processed' => 'المرحلة الثانية - المعالج',
                                        'stage3_coils' => 'المرحلة الثالثة - اللفائف',
                                        'stage4_boxes' => 'المرحلة الرابعة - الصناديق',
                                    ];
                                @endphp
                                <div class="timeline-item {{ $log->is_active ? 'active' : '' }} mb-4">
                                    <div class="row">
                                        <div class="col-md-2 text-end">
                                            <div class="timeline-date">
                                                <small class="text-muted d-block">{{ $log->started_at->format('Y-m-d') }}</small>
                                                <strong>{{ $log->started_at->format('H:i') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <div class="timeline-icon">
                                                @if($log->is_active)
                                                    <div class="badge bg-success rounded-circle p-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="badge bg-primary rounded-circle p-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card shadow-sm {{ $log->is_active ? 'border-success' : '' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="mb-0">
                                                            <span class="badge bg-info me-2">{{ $stageNames[$log->stage_type] ?? $log->stage_type }}</span>
                                                            <span class="badge bg-{{ $log->is_active ? 'success' : 'primary' }}">
                                                                {{ $log->is_active ? 'نشط' : 'منتهي' }}
                                                            </span>
                                                        </h5>
                                                        <span class="badge bg-light text-dark">{{ $log->worker_type == 'individual' ? 'فردي' : 'فريق' }}</span>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label text-muted small mb-1">العامل</label>
                                                            <div class="d-flex align-items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check text-success me-2" viewBox="0 0 16 16">
                                                                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                                                                    <path d="M8.256 14a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Z"/>
                                                                </svg>
                                                                <strong>{{ $log->worker ? $log->worker->name : 'غير محدد' }}</strong>
                                                            </div>
                                                        </div>

                                                        @if($log->assignedBy)
                                                            <div class="col-md-6">
                                                                <label class="form-label text-muted small mb-1">قام بالتعيين</label>
                                                                <div>{{ $log->assignedBy->name }}</div>
                                                            </div>
                                                        @endif

                                                        @if($log->ended_at)
                                                            <div class="col-md-6">
                                                                <label class="form-label text-muted small mb-1">تاريخ الانتهاء</label>
                                                                <div>{{ $log->ended_at->format('Y-m-d H:i') }}</div>
                                                            </div>
                                                        @endif

                                                        @if($log->duration_minutes)
                                                            <div class="col-md-6">
                                                                <label class="form-label text-muted small mb-1">المدة</label>
                                                                <div>
                                                                    @php
                                                                        $hours = floor($log->duration_minutes / 60);
                                                                        $minutes = $log->duration_minutes % 60;
                                                                    @endphp
                                                                    @if($hours > 0)
                                                                        {{ $hours }} ساعة
                                                                    @endif
                                                                    @if($minutes > 0)
                                                                        {{ $minutes }} دقيقة
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($log->status_before || $log->status_after)
                                                            <div class="col-12">
                                                                <label class="form-label text-muted small mb-1">الحالة</label>
                                                                <div>
                                                                    @if($log->status_before)
                                                                        <span class="badge bg-secondary me-2">قبل: {{ $log->status_before }}</span>
                                                                    @endif
                                                                    @if($log->status_after)
                                                                        <span class="badge bg-primary">بعد: {{ $log->status_after }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($log->notes)
                                                            <div class="col-12">
                                                                <label class="form-label text-muted small mb-1">الملاحظات</label>
                                                                <div class="alert alert-info mb-0 py-2">{{ $log->notes }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 60px;
    width: 2px;
    height: calc(100% - 20px);
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transform: translateX(-50%);
}

.timeline-date {
    padding: 10px;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}
</style>
@endsection
