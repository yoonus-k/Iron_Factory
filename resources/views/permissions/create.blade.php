@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('permissions.add_new_permission') }}</h2>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('permissions.back') }}
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

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('permissions.permission_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="permission_name" class="form-control" value="{{ old('permission_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('permissions.permission_name_en') }}</label>
                        <input type="text" name="permission_name_en" class="form-control" value="{{ old('permission_name_en') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('permissions.permission_code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="permission_code" class="form-control" value="{{ old('permission_code') }}" required>
                        <small class="form-text text-muted">{{ __('permissions.code_will_be_uppercase') }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('permissions.module') }} <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control" value="{{ old('module') }}" list="modules" required>
                        <datalist id="modules">
                            @foreach($modules as $module)
                            <option value="{{ $module }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">{{ __('permissions.description') }}</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('permissions.save_permission') }}
            </button>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">{{ __('permissions.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
