@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">تعديل الصلاحية: {{ $permission->permission_name }}</h2>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
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

    @if($permission->is_system)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        هذه صلاحية نظام. الكود محمي ولا يمكن تعديله.
    </div>
    @endif

    <form action="{{ route('permissions.update', $permission) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">اسم الصلاحية <span class="text-danger">*</span></label>
                        <input type="text" name="permission_name" class="form-control" value="{{ old('permission_name', $permission->permission_name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="permission_name_en" class="form-control" value="{{ old('permission_name_en', $permission->permission_name_en) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الكود</label>
                        <input type="text" class="form-control" value="{{ $permission->permission_code }}" disabled>
                        <small class="form-text text-muted">لا يمكن تعديل الكود</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوحدة <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control" value="{{ old('module', $permission->module) }}" list="modules" required>
                        <datalist id="modules">
                            @foreach($modules as $module)
                            <option value="{{ $module }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $permission->description) }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   {{ $permission->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
