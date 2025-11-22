@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إدارة الأدوار</h2>
        @if(canCreate('MANAGE_ROLES'))
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة دور جديد
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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الرقم</th>
                            <th>اسم الدور</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>الكود</th>
                            <th>المستوى</th>
                            <th>عدد الصلاحيات</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                {{ $role->role_name }}
                                @if($role->is_system)
                                <span class="badge bg-info">نظام</span>
                                @endif
                            </td>
                            <td>{{ $role->role_name_en ?? '-' }}</td>
                            <td><code>{{ $role->role_code }}</code></td>
                            <td>
                                <span class="badge bg-secondary">{{ $role->level }}</span>
                            </td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td>
                                @if($role->is_active)
                                <span class="badge bg-success">نشط</span>
                                @else
                                <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(canUpdate('MANAGE_ROLES'))
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary" 
                                       @if($role->is_system) onclick="return confirm('هذا دور نظام. بعض الإعدادات لا يمكن تعديلها')" @endif>
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(canDelete('MANAGE_ROLES') && !$role->is_system)
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
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
                            <td colspan="8" class="text-center">لا توجد أدوار</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
