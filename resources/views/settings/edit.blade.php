@extends('master')

@section('title', 'تعديل الإعدادات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                تعديل إعدادات النظام
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i>
                رجوع
            </a>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        @foreach($settings as $category => $categorySettings)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-folder me-2"></i>
                    {{ ucfirst($category) }}
                </h5>
            </div>
            <div class="card-body">
                @foreach($categorySettings as $setting)
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">
                        <strong>{{ $setting->setting_key }}</strong>
                        @if($setting->description)
                        <br><small class="text-muted">{{ $setting->description }}</small>
                        @endif
                    </label>
                    <div class="col-md-6">
                        @if($setting->setting_type === 'boolean')
                            <select name="settings[{{ $setting->setting_key }}]" class="form-select">
                                <option value="1" {{ $setting->setting_value == '1' || $setting->setting_value == 'true' ? 'selected' : '' }}>نعم</option>
                                <option value="0" {{ $setting->setting_value == '0' || $setting->setting_value == 'false' ? 'selected' : '' }}>لا</option>
                            </select>
                        @elseif($setting->setting_type === 'number')
                            <input type="number" 
                                   name="settings[{{ $setting->setting_key }}]" 
                                   class="form-control" 
                                   value="{{ $setting->setting_value }}"
                                   step="any">
                        @elseif($setting->setting_type === 'json')
                            <textarea name="settings[{{ $setting->setting_key }}]" 
                                      class="form-control" 
                                      rows="4">{{ $setting->setting_value }}</textarea>
                        @else
                            <input type="text" 
                                   name="settings[{{ $setting->setting_key }}]" 
                                   class="form-control" 
                                   value="{{ $setting->setting_value }}">
                        @endif
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-info">{{ $setting->setting_type }}</span>
                        @if($setting->is_public)
                            <span class="badge bg-success">عام</span>
                        @endif
                    </div>
                </div>
                <hr>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="card">
            <div class="card-body text-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-2"></i>
                    حفظ التغييرات
                </button>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-2"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
