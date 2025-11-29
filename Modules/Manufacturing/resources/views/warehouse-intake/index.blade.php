@extends('master')

@section('title', 'جميع طلبات الإدخال')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                طلبات إدخال المستودع
            </h2>
            <p class="text-muted mb-0">سجل جميع طلبات إدخال الصناديق للمستودع</p>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->hasPermission('WAREHOUSE_INTAKE_CREATE'))
            <a href="{{ route('manufacturing.warehouse-intake.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>
                طلب إدخال جديد
            </a>
            @endif
            @if(Auth::user()->hasPermission('WAREHOUSE_INTAKE_APPROVE'))
            <a href="{{ route('manufacturing.warehouse-intake.pending-approval') }}" class="btn btn-warning">
                <i class="bi bi-hourglass-split me-1"></i>
                المعلقة
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>الحالة</th>
                            <th>الصناديق</th>
                            <th>الوزن</th>
                            <th>المُنشئ</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td><strong>{{ $request->request_number }}</strong></td>
                            <td>
                                @if($request->status === 'pending')
                                    <span class="badge bg-warning">قيد الانتظار</span>
                                @elseif($request->status === 'approved')
                                    <span class="badge bg-success">معتمد</span>
                                @else
                                    <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </td>
                            <td>{{ $request->boxes_count }}</td>
                            <td>{{ number_format($request->total_weight, 2) }} كجم</td>
                            <td>{{ $request->requestedBy->name ?? '-' }}</td>
                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manufacturing.warehouse-intake.show', $request->id) }}" 
                                       class="btn btn-sm btn-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye me-1"></i>عرض
                                    </a>
                                    @if($request->status === 'approved')
                                    <a href="{{ route('manufacturing.warehouse-intake.print', $request->id) }}" 
                                       class="btn btn-sm btn-secondary" target="_blank" title="طباعة">
                                        <i class="bi bi-printer me-1"></i>طباعة
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">لا توجد طلبات</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
