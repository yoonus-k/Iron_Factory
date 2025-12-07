@extends('master')

@section('title', 'جميع طلبات الإدخال')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    .page-header h2 {
        margin: 0;
        font-weight: 700;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .action-btn {
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .table-card {
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .table thead {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .table tbody tr {
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.005);
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1">
                    <i class="bi bi-box-seam me-2"></i>
                    طلبات إدخال المستودع
                </h2>
                <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">إدارة ومتابعة جميع طلبات إدخال الصناديق للمستودع</p>
            </div>
            <div>
                @if(Auth::user()->hasPermission('WAREHOUSE_INTAKE_CREATE'))
                <a href="{{ route('manufacturing.warehouse-intake.create') }}" class="btn btn-light action-btn">
                    <i class="bi bi-plus-circle-fill me-1"></i>
                    طلب إدخال جديد
                </a>
                @endif
                @if(Auth::user()->hasPermission('WAREHOUSE_INTAKE_APPROVE'))
                <a href="{{ route('manufacturing.warehouse-intake.pending-approval') }}" class="btn btn-warning action-btn">
                    <i class="bi bi-hourglass-split me-1"></i>
                    الطلبات المعلقة
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card table-card border-0">
        <div class="card-body p-0">
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
                                    <span class="badge bg-warning status-badge"><i class="bi bi-clock me-1"></i>قيد الانتظار</span>
                                @elseif($request->status === 'approved')
                                    <span class="badge bg-success status-badge"><i class="bi bi-check-circle me-1"></i>معتمد</span>
                                @else
                                    <span class="badge bg-danger status-badge"><i class="bi bi-x-circle me-1"></i>مرفوض</span>
                                @endif
                            </td>
                            <td>{{ $request->boxes_count }}</td>
                            <td>{{ number_format($request->total_weight, 2) }} كجم</td>
                            <td>{{ $request->requestedBy->name ?? '-' }}</td>
                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manufacturing.warehouse-intake.show', $request->id) }}" 
                                       class="btn btn-sm btn-info action-btn" title="عرض التفاصيل">
                                        <i class="bi bi-eye-fill me-1"></i>عرض
                                    </a>
                                    @if($request->status === 'approved')
                                    <a href="{{ route('manufacturing.warehouse-intake.print', $request->id) }}" 
                                       class="btn btn-sm btn-secondary action-btn" target="_blank" title="طباعة">
                                        <i class="bi bi-printer-fill me-1"></i>طباعة
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div style="opacity: 0.5;">
                                    <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d;"></i>
                                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #6c757d;">لا توجد طلبات إدخال</p>
                                    <small class="text-muted">ابدأ بإنشاء طلب جديد</small>
                                </div>
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
