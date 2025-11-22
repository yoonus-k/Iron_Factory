{{-- مثال: صفحة المرحلة الأولى مع الصلاحيات التفصيلية --}}
@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="card">
        <div class="card-header">
            <h5>تقسيم المواد إلى استاندات - المرحلة الأولى</h5>
        </div>
        <div class="card-body">
            <form id="stage1Form">
                @csrf
                
                {{-- الباركود - يظهر للجميع --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">باركود المادة</label>
                        <input type="text" name="barcode" id="barcode" class="form-control" required>
                    </div>
                </div>

                {{-- معلومات المادة الأساسية - تظهر للجميع --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">اسم المادة</label>
                        <input type="text" id="materialName" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">المورد</label>
                        <input type="text" id="supplierName" class="form-control" readonly>
                    </div>
                </div>

                {{-- تفاصيل الوزن - تظهر فقط لمن لديه صلاحية STAGE1_VIEW_WEIGHT --}}
                @if(canRead('STAGE1_VIEW_WEIGHT'))
                <div class="row mb-3 border rounded p-3 bg-light">
                    <div class="col-12 mb-2">
                        <h6 class="text-primary">
                            <i class="fas fa-weight"></i> تفاصيل الوزن
                        </h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الوزن الإجمالي</label>
                        <input type="number" id="totalWeight" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الوزن المتبقي</label>
                        <input type="number" id="remainingWeight" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نسبة الهدر</label>
                        <input type="text" id="wastePercentage" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الوزن المهدر</label>
                        <input type="number" id="wasteWeight" class="form-control" readonly>
                    </div>
                </div>
                @endif

                {{-- حقل تعديل الوزن - يظهر فقط لمن لديه صلاحية STAGE1_EDIT_WEIGHT --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">وزن الاستاند الجديد <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="stand_weight" 
                               id="standWeight" 
                               class="form-control" 
                               step="0.01" 
                               required
                               @if(!canCreate('STAGE1_EDIT_WEIGHT')) readonly @endif>
                        @if(!canCreate('STAGE1_EDIT_WEIGHT'))
                        <small class="text-muted">لا يمكنك تعديل الوزن (تحتاج صلاحية)</small>
                        @endif
                    </div>
                </div>

                {{-- معلومات العامل - تظهر فقط لمن لديه صلاحية STAGE1_VIEW_WORKER --}}
                @if(canRead('STAGE1_VIEW_WORKER'))
                <div class="row mb-3 border rounded p-3">
                    <div class="col-12 mb-2">
                        <h6 class="text-info">
                            <i class="fas fa-user"></i> معلومات العامل
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">اسم العامل</label>
                        <select name="worker_id" class="form-control">
                            <option value="">اختر العامل</option>
                            {{-- قائمة العمال --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الوردية</label>
                        <input type="text" id="workerShift" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الوقت</label>
                        <input type="text" value="{{ now()->format('Y-m-d H:i') }}" class="form-control" readonly>
                    </div>
                </div>
                @endif

                {{-- معلومات الأسعار - تظهر فقط لمن لديه صلاحية VIEW_PRICES --}}
                @if(canRead('VIEW_PRICES'))
                <div class="row mb-3 border rounded p-3 bg-warning bg-opacity-10">
                    <div class="col-12 mb-2">
                        <h6 class="text-warning">
                            <i class="fas fa-dollar-sign"></i> معلومات السعر
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">سعر الكيلو</label>
                        <input type="number" 
                               id="pricePerKg" 
                               class="form-control" 
                               @if(!canUpdate('EDIT_PRICES')) readonly @endif>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">التكلفة الإجمالية</label>
                        <input type="number" id="totalCost" class="form-control" readonly>
                    </div>
                </div>
                @endif

                {{-- التكاليف - تظهر فقط لمن لديه صلاحية VIEW_COSTS --}}
                @if(canRead('VIEW_COSTS'))
                <div class="alert alert-info">
                    <strong>إجمالي التكلفة:</strong> <span id="totalProductionCost">0</span> ريال
                </div>
                @endif

                {{-- أزرار الإجراءات --}}
                <div class="d-flex gap-2 mt-4">
                    @if(canCreate('STAGE1_STANDS'))
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ
                    </button>
                    @endif

                    <button type="button" class="btn btn-secondary" onclick="printBarcode()">
                        <i class="fas fa-print"></i> طباعة الباركود
                    </button>

                    @if(canDelete('DELETE_RECORDS'))
                    <button type="button" class="btn btn-danger" onclick="deleteRecord()">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// يمكنك التحكم في العرض من JavaScript أيضاً
const userPermissions = {
    canViewWeight: {{ canRead('STAGE1_VIEW_WEIGHT') ? 'true' : 'false' }},
    canEditWeight: {{ canCreate('STAGE1_EDIT_WEIGHT') ? 'true' : 'false' }},
    canViewWorker: {{ canRead('STAGE1_VIEW_WORKER') ? 'true' : 'false' }},
    canViewPrices: {{ canRead('VIEW_PRICES') ? 'true' : 'false' }},
    canEditPrices: {{ canUpdate('EDIT_PRICES') ? 'true' : 'false' }},
    canViewCosts: {{ canRead('VIEW_COSTS') ? 'true' : 'false' }},
    canDelete: {{ canDelete('DELETE_RECORDS') ? 'true' : 'false' }}
};

console.log('صلاحيات المستخدم:', userPermissions);
</script>
@endsection
