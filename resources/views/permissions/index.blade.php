@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إدارة الصلاحيات</h2>
        @if(canCreate('MANAGE_PERMISSIONS'))
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة صلاحية جديدة
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">تصفية حسب الوحدة:</label>
                <select class="form-select" id="moduleFilter" onchange="filterByModule()">
                    <option value="">جميع الوحدات</option>
                    @foreach($modules as $module)
                    <option value="{{ $module }}">{{ $module }}</option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الرقم</th>
                            <th>اسم الصلاحية</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>الكود</th>
                            <th>الوحدة</th>
                            <th>عدد الأدوار</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                        <tr data-module="{{ $permission->module }}">
                            <td>{{ $permission->id }}</td>
                            <td>
                                {{ $permission->permission_name }}
                                @if($permission->is_system)
                                <span class="badge bg-info">نظام</span>
                                @endif
                            </td>
                            <td>{{ $permission->permission_name_en ?? '-' }}</td>
                            <td><code>{{ $permission->permission_code }}</code></td>
                            <td><span class="badge bg-secondary">{{ $permission->module }}</span></td>
                            <td>{{ $permission->roles->count() }}</td>
                            <td>
                                @if($permission->is_active)
                                <span class="badge bg-success">نشط</span>
                                @else
                                <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(canUpdate('MANAGE_PERMISSIONS'))
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(canDelete('MANAGE_PERMISSIONS') && !$permission->is_system)
                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الصلاحية؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد صلاحيات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function filterByModule() {
    const module = document.getElementById('moduleFilter').value;
    const rows = document.querySelectorAll('tbody tr[data-module]');
    
    rows.forEach(row => {
        if (module === '' || row.dataset.module === module) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
