@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">تعديل الدور: {{ $role->role_name }}</h2>
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

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الأساسية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">اسم الدور <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" class="form-control" value="{{ old('role_name', $role->role_name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="role_name_en" class="form-control" value="{{ old('role_name_en', $role->role_name_en) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الكود</label>
                        <input type="text" class="form-control" value="{{ $role->role_code }}" disabled>
                        <small class="form-text text-muted">لا يمكن تعديل الكود</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المستوى (0-100) <span class="text-danger">*</span></label>
                        <input type="number" name="level" class="form-control" min="0" max="100"
                               value="{{ old('level', $role->level) }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                   {{ $role->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الصلاحيات</h5>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> صلاحيات القوائم تحتاج فقط إلى "قراءة" لإظهار القائمة
                </small>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Sidebar with module list -->
                    <div class="col-md-3">
                        <div class="list-group sticky-top" role="tablist" style="top: 20px;">
                            @forelse($permissions as $module => $modulePermissions)
                            <button class="list-group-item list-group-item-action module-tab-btn @if($loop->first) active @endif"
                                    id="module-{{ $loop->index }}-tab"
                                    data-bs-toggle="list"
                                    href="#module-{{ $loop->index }}"
                                    role="tab"
                                    data-module-index="{{ $loop->index }}">
                                <i class="fas fa-folder-open ms-2"></i>{{ $module }}
                                <span class="badge bg-secondary ms-auto module-count-{{ $loop->index }}">
                                    {{ $role->permissions->whereIn('id', $modulePermissions->pluck('id'))->count() }}
                                </span>
                            </button>
                            @empty
                            <p class="text-muted">لا توجد صلاحيات</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Content area showing selected module's permissions -->
                    <div class="col-md-9">
                        <div class="tab-content">
                            @foreach($permissions as $module => $modulePermissions)
                            <div class="tab-pane fade @if($loop->first) show active @endif"
                                 id="module-{{ $loop->index }}"
                                 role="tabpanel"
                                 data-module-index="{{ $loop->index }}">
                                <div class="mb-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-lock ms-2"></i>صلاحيات {{ $module }}
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="35%">الصلاحية</th>
                                                    <th class="text-center" width="11%">
                                                        <i class="fas fa-plus-circle" title="إنشاء"></i>
                                                    </th>
                                                    <th class="text-center" width="11%">
                                                        <i class="fas fa-eye" title="قراءة"></i>
                                                    </th>
                                                    <th class="text-center" width="11%">
                                                        <i class="fas fa-edit" title="تعديل"></i>
                                                    </th>
                                                    <th class="text-center" width="11%">
                                                        <i class="fas fa-trash" title="حذف"></i>
                                                    </th>
                                                    <th class="text-center" width="11%">
                                                        <i class="fas fa-check-circle" title="موافقة"></i>
                                                    </th>
                                                    <th class="text-center" width="10%">
                                                        <i class="fas fa-download" title="تصدير"></i>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($modulePermissions as $permission)
                                                @php
                                                    $rolePermission = $role->permissions->find($permission->id);

                                                    // صلاحيات القوائم تحتاج فقط القراءة
                                                    $isMenuPermission = str_starts_with($permission->permission_code, 'MENU_');

                                                    $viewOnlyPermissions = ['VIEW_MAIN_DASHBOARD', 'VIEW_DAILY_REPORTS', 'VIEW_WASTE_REPORTS', 'VIEW_SHIFT_REPORTS', 'VIEW_NOTIFICATIONS', 'VIEW_ACTIVITY_LOG', 'VIEW_PRICES', 'VIEW_COSTS', 'VIEW_DASHBOARD', 'VIEW_REPORTS'];
                                                    $manageOnlyPermissions = ['MANAGE_SYSTEM_SETTINGS', 'MANAGE_BARCODE_SETTINGS', 'MANAGE_BACKUP'];
                                                    $printOnlyPermissions = ['PRINT_BARCODE'];
                                                    $viewWeightPermissions = ['STAGE1_VIEW_WEIGHT', 'STAGE2_VIEW_WEIGHT', 'STAGE3_VIEW_WEIGHT', 'STAGE4_VIEW_WEIGHT'];
                                                    $editWeightPermissions = ['STAGE1_EDIT_WEIGHT', 'STAGE2_EDIT_WEIGHT', 'STAGE3_EDIT_WEIGHT', 'STAGE4_EDIT_WEIGHT'];
                                                    $viewWorkerPermissions = ['STAGE1_VIEW_WORKER', 'STAGE2_VIEW_WORKER', 'STAGE3_VIEW_WORKER', 'STAGE4_VIEW_WORKER'];

                                                    $isViewOnly = $isMenuPermission || in_array($permission->permission_code, $viewOnlyPermissions) || in_array($permission->permission_code, $viewWeightPermissions) || in_array($permission->permission_code, $viewWorkerPermissions);
                                                    $isManageOnly = in_array($permission->permission_code, $manageOnlyPermissions) || in_array($permission->permission_code, $editWeightPermissions) || $permission->permission_code == 'EDIT_PRICES';
                                                    $isPrintOnly = in_array($permission->permission_code, $printOnlyPermissions);
                                                    $isDeleteRecords = $permission->permission_code == 'DELETE_RECORDS';
                                                @endphp
                                                <tr>
                                                    <td class="align-middle">
                                                        <small>{{ $permission->permission_name }}</small>
                                                        @if($isMenuPermission)
                                                        <br><small class="badge bg-info">صلاحية قائمة</small>
                                                        @endif
                                                        <input type="hidden" name="permissions[{{ $permission->id }}][permission_id]" value="{{ $permission->id }}">
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_create]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               {{ $rolePermission && $rolePermission->pivot->can_create ? 'checked' : '' }}>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_read]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               @if($isMenuPermission) title="مطلوب لإظهار القائمة" @endif
                                                               {{ $rolePermission && $rolePermission->pivot->can_read ? 'checked' : '' }}>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isViewOnly && !$isPrintOnly && !$isDeleteRecords)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_update]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               {{ $rolePermission && $rolePermission->pivot->can_update ? 'checked' : '' }}>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_delete]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               {{ $rolePermission && $rolePermission->pivot->can_delete ? 'checked' : '' }}>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isViewOnly && !$isManageOnly && !$isPrintOnly && !$isDeleteRecords)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_approve]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               {{ $rolePermission && $rolePermission->pivot->can_approve ? 'checked' : '' }}>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(!$isDeleteRecords)
                                                        <input type="checkbox"
                                                               name="permissions[{{ $permission->id }}][can_export]"
                                                               value="1"
                                                               class="form-check-input permission-checkbox"
                                                               data-module="{{ $loop->parent->index }}"
                                                               {{ $rolePermission && $rolePermission->pivot->can_export ? 'checked' : '' }}>
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
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<style>
    .module-tab-btn {
        text-align: right;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .module-tab-btn:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .module-tab-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .module-tab-btn .badge {
        font-size: 0.75rem;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-top: 0.25rem;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    @media (max-width: 768px) {
        .sticky-top {
            position: static !important;
            margin-bottom: 1.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تحديث العدادات عند تحميل الصفحة
        updateAllModuleCounts();

        // تحديث العدادات عند تغيير الـ checkboxes
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const moduleIndex = this.getAttribute('data-module');
                updateModuleCount(moduleIndex);
            });
        });

        function updateModuleCount(moduleIndex) {
            const modulePane = document.querySelector(`[data-module-index="${moduleIndex}"]`);
            if (!modulePane) return;

            const checkedCount = modulePane.querySelectorAll('.permission-checkbox:checked').length;
            const badgeElement = document.querySelector(`.module-count-${moduleIndex}`);

            if (badgeElement) {
                badgeElement.textContent = checkedCount;
                if (checkedCount > 0) {
                    badgeElement.classList.remove('bg-secondary');
                    badgeElement.classList.add('bg-success');
                } else {
                    badgeElement.classList.remove('bg-success');
                    badgeElement.classList.add('bg-secondary');
                }
            }
        }

        function updateAllModuleCounts() {
            const modulePanes = document.querySelectorAll('[data-module-index]');
            modulePanes.forEach(pane => {
                const moduleIndex = pane.getAttribute('data-module-index');
                updateModuleCount(moduleIndex);
            });
        }
    });
</script>
@endsection
