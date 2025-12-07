@extends('master')

@section('title', 'تفاصيل طلب الإدخال')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                تفاصيل طلب الإدخال
            </h2>
            <p class="text-muted mb-0">
                رقم الطلب: <strong class="text-primary">{{ $intakeRequest->request_number }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.warehouse-intake.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة
            </a>
            @if($intakeRequest->canPrint())
            <a href="{{ route('manufacturing.warehouse-intake.print', $intakeRequest->id) }}" 
               class="btn btn-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i>
                طباعة
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- الحالة -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">الحالة</h6>
                            @if($intakeRequest->status === 'pending')
                                <h4><span class="badge bg-warning">قيد الانتظار</span></h4>
                            @elseif($intakeRequest->status === 'approved')
                                <h4><span class="badge bg-success">معتمد</span></h4>
                            @else
                                <h4><span class="badge bg-danger">مرفوض</span></h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- الصناديق -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        الصناديق ({{ $intakeRequest->items->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الباركود</th>
                                    <th>التغليف</th>
                                    <th>المواصفات</th>
                                    <th>الوزن</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalWeight = 0; @endphp
                                @foreach($intakeRequest->items as $index => $item)
                                @php 
                                    $totalWeight += $item->weight;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong class="text-primary">{{ $item->barcode }}</strong></td>
                                    <td>{{ $item->packaging_type }}</td>
                                    <td>
                                        @if(isset($item->materials) && $item->materials->count() > 0)
                                            @foreach($item->materials as $material)
                                                <span class="badge bg-info me-1">
                                                    @if($material->color) {{ $material->color }} @endif
                                                    @if($material->plastic_type) - {{ $material->plastic_type }} @endif
                                                    @if($material->wire_size) - {{ $material->wire_size }} @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($item->weight, 2) }} كجم</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>الإجمالي:</strong></td>
                                    <td colspan="2"><strong class="text-success">{{ number_format($totalWeight, 2) }} كجم</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($intakeRequest->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard me-2"></i>
                        الملاحظات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $intakeRequest->notes }}</p>
                </div>
            </div>
            @endif

            @if($intakeRequest->status === 'rejected' && $intakeRequest->rejection_reason)
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        سبب الرفض
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $intakeRequest->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">معلومات الطلب</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">تاريخ الإنشاء</label>
                        <div><strong>{{ $intakeRequest->created_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">المُنشئ</label>
                        <div><strong>{{ $intakeRequest->requestedBy->name ?? '-' }}</strong></div>
                    </div>

                    @if($intakeRequest->approved_at)
                    <div class="mb-3">
                        <label class="text-muted small">تاريخ الاعتماد</label>
                        <div><strong>{{ $intakeRequest->approved_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">المعتمد من قبل</label>
                        <div><strong>{{ $intakeRequest->approvedBy->name ?? '-' }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">إحصائيات</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>عدد الصناديق:</span>
                        <strong>{{ $intakeRequest->boxes_count }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>الوزن الإجمالي:</span>
                        <strong>{{ number_format($intakeRequest->total_weight, 2) }} كجم</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
