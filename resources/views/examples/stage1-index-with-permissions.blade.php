{{-- مثال: صفحة Index للمرحلة الأولى مع الصلاحيات التفصيلية --}}
@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>قائمة الاستاندات - المرحلة الأولى</h2>
        
        @if(canCreate('STAGE1_STANDS'))
        <a href="{{ route('manufacturing.stage1.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة جديد
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الباركود</th>
                            <th>اسم المادة</th>
                            <th>المورد</th>
                            
                            {{-- عمود الوزن - يظهر فقط لمن لديه صلاحية --}}
                            @if(canRead('STAGE1_VIEW_WEIGHT'))
                            <th>الوزن</th>
                            <th>الهدر</th>
                            @endif
                            
                            {{-- عمود العامل - يظهر فقط لمن لديه صلاحية --}}
                            @if(canRead('STAGE1_VIEW_WORKER'))
                            <th>العامل</th>
                            <th>الوردية</th>
                            @endif
                            
                            {{-- عمود السعر - يظهر فقط لمن لديه صلاحية --}}
                            @if(canRead('VIEW_PRICES'))
                            <th>السعر</th>
                            @endif
                            
                            {{-- عمود التكلفة - يظهر فقط لمن لديه صلاحية --}}
                            @if(canRead('VIEW_COSTS'))
                            <th>التكلفة</th>
                            @endif
                            
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stands as $stand)
                        <tr>
                            <td><code>{{ $stand->barcode }}</code></td>
                            <td>{{ $stand->material->material_name ?? '-' }}</td>
                            <td>{{ $stand->material->supplier->supplier_name ?? '-' }}</td>
                            
                            {{-- بيانات الوزن --}}
                            @if(canRead('STAGE1_VIEW_WEIGHT'))
                            <td>{{ number_format($stand->final_weight, 2) }} كجم</td>
                            <td>
                                <span class="badge bg-{{ $stand->waste_weight > 10 ? 'danger' : 'warning' }}">
                                    {{ number_format($stand->waste_weight, 2) }} كجم
                                </span>
                            </td>
                            @endif
                            
                            {{-- بيانات العامل --}}
                            @if(canRead('STAGE1_VIEW_WORKER'))
                            <td>{{ $stand->user->name ?? '-' }}</td>
                            <td>{{ $stand->shift ?? '-' }}</td>
                            @endif
                            
                            {{-- السعر --}}
                            @if(canRead('VIEW_PRICES'))
                            <td>{{ number_format($stand->price_per_kg ?? 0, 2) }} ريال</td>
                            @endif
                            
                            {{-- التكلفة --}}
                            @if(canRead('VIEW_COSTS'))
                            <td>
                                <strong>{{ number_format($stand->total_cost ?? 0, 2) }} ريال</strong>
                            </td>
                            @endif
                            
                            <td>{{ $stand->created_at->format('Y-m-d H:i') }}</td>
                            
                            <td>
                                <div class="btn-group btn-group-sm">
                                    {{-- زر العرض --}}
                                    @if(canRead('STAGE1_STANDS'))
                                    <a href="{{ route('manufacturing.stage1.show', $stand) }}" 
                                       class="btn btn-info" 
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    
                                    {{-- زر التعديل --}}
                                    @if(canUpdate('STAGE1_STANDS'))
                                    <a href="{{ route('manufacturing.stage1.edit', $stand) }}" 
                                       class="btn btn-primary" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    {{-- زر الطباعة --}}
                                    <button onclick="printBarcode('{{ $stand->barcode }}')" 
                                            class="btn btn-secondary" 
                                            title="طباعة">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    
                                    {{-- زر الحذف - يظهر فقط لمن لديه صلاحية --}}
                                    @if(canDelete('DELETE_RECORDS'))
                                    <form action="{{ route('manufacturing.stage1.destroy', $stand) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100" class="text-center">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- إحصائيات - تظهر فقط لمن لديه صلاحية --}}
            @if(canRead('STAGE1_VIEW_WEIGHT'))
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>إجمالي الوزن</h6>
                            <h3>{{ number_format($totalWeight ?? 0, 2) }} كجم</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>إجمالي الهدر</h6>
                            <h3>{{ number_format($totalWaste ?? 0, 2) }} كجم</h3>
                        </div>
                    </div>
                </div>
                @if(canRead('VIEW_COSTS'))
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>إجمالي التكلفة</h6>
                            <h3>{{ number_format($totalCost ?? 0, 2) }} ريال</h3>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>عدد الاستاندات</h6>
                            <h3>{{ $stands->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- الترقيم --}}
            <div class="mt-3">
                {{ $stands->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function printBarcode(barcode) {
    window.open('/manufacturing/barcode/print/' + barcode, '_blank');
}
</script>
@endsection
