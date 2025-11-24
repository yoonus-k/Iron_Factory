@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إضافة دور جديد</h2>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الأساسية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">اسم الدور <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" class="form-control" value="{{ old('role_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="role_name_en" class="form-control" value="{{ old('role_name_en') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الكود <span class="text-danger">*</span></label>
                        <input type="text" name="role_code" class="form-control" value="{{ old('role_code') }}" required>
                        <small class="form-text text-muted">سيتم تحويله إلى أحرف كبيرة تلقائياً</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المستوى (0-100) <span class="text-danger">*</span></label>
                        <input type="number" name="level" class="form-control" min="0" max="100" value="{{ old('level', 50) }}" required>
                        <small class="form-text text-muted">المستوى الأعلى له صلاحيات أكثر</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">الصلاحيات</h5>
            </div>
            <div class="card-body">
                @foreach($permissions as $module => $modulePermissions)
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">{{ $module }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="30%">الصلاحية</th>
                                    <th class="text-center">إنشاء</th>
                                    <th class="text-center">قراءة</th>
                                    <th class="text-center">تعديل</th>
                                    <th class="text-center">حذف</th>
                                    <th class="text-center">موافقة</th>
                                    <th class="text-center">تصدير</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modulePermissions as $permission)
                                @php
                                    // تحديد الصلاحيات التي لا تحتاج إلى إنشاء/تعديل/حذف
                                    $viewOnlyPermissions = ['VIEW_MAIN_DASHBOARD', 'VIEW_DAILY_REPORTS', 'VIEW_WASTE_REPORTS', 'VIEW_SHIFT_REPORTS', 'VIEW_NOTIFICATIONS', 'VIEW_ACTIVITY_LOG', 'VIEW_PRICES', 'VIEW_COSTS'];
                                    $manageOnlyPermissions = ['MANAGE_SYSTEM_SETTINGS', 'MANAGE_BARCODE_SETTINGS', 'MANAGE_BACKUP'];
                                    $printOnlyPermissions = ['PRINT_BARCODE'];
                                    $viewWeightPermissions = ['STAGE1_VIEW_WEIGHT', 'STAGE2_VIEW_WEIGHT', 'STAGE3_VIEW_WEIGHT', 'STAGE4_VIEW_WEIGHT'];
                                    $editWeightPermissions = ['STAGE1_EDIT_WEIGHT', 'STAGE2_EDIT_WEIGHT', 'STAGE3_EDIT_WEIGHT', 'STAGE4_EDIT_WEIGHT'];
                                    $viewWorkerPermissions = ['STAGE1_VIEW_WORKER', 'STAGE2_VIEW_WORKER', 'STAGE3_VIEW_WORKER', 'STAGE4_VIEW_WORKER'];

                                    $isViewOnly = in_array($permission->permission_code, $viewOnlyPermissions) || in_array($permission->permission_code, $viewWeightPermissions) || in_array($permission->permission_code, $viewWorkerPermissions);
                                    $isManageOnly = in_array($permission->permission_code, $manageOnlyPermissions) || in_array($permission->permission_code, $editWeightPermissions) || $permission->permission_code == 'EDIT_PRICES';
                                    $isPrintOnly = in_array($permission->permission_code, $printOnlyPermissions);
                                    $isDeleteRecords = $permission->permission_code == 'DELETE_RECORDS';
                                @endphp
                                <tr>
                                    <td>{{ $permission->permission_name }}</td>
                                    <td class="text-center">
                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_create]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                        <input type="hidden" name="permissions[{{ $permission->id }}][permission_id]" value="{{ $permission->id }}">
                                    </td>
                                    <td class="text-center">
                                        @if(!$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_read]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$isViewOnly && !$isPrintOnly && !$isDeleteRecords)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_update]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_delete]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_approve]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$isDeleteRecords)
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][can_export]" value="1">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ الدور
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<style>
    .table input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
@endsection
