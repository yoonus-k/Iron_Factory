@extends('master')

@section('title', __('settings.system_settings'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-cog me-2"></i>
                {{ __('settings.system_settings') }}
            </h2>
            <p class="text-muted mb-0">{{ __('settings.manage_settings') }}</p>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->hasPermission('SETTINGS_UPDATE'))
            <a href="{{ route('settings.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>
                {{ __('settings.edit_settings') }}
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') ?? __('settings.settings_updated') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') ?? __('settings.settings_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @forelse($settings as $category => $categorySettings)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-folder me-2"></i>
                {{ ucfirst($category) }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="25%">{{ __('settings.key') }}</th>
                            <th width="25%">{{ __('settings.value') }}</th>
                            <th width="35%">{{ __('settings.description') }}</th>
                            <th width="10%">{{ __('settings.type') }}</th>
                            <th width="5%">{{ __('settings.public') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorySettings as $setting)
                        <tr>
                            <td><code>{{ $setting->setting_key }}</code></td>
                            <td>
                                @if($setting->setting_type === 'boolean')
                                    @if($setting->setting_value === '1' || $setting->setting_value === 'true')
                                        <span class="badge bg-success">{{ __('settings.yes') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('settings.no') }}</span>
                                    @endif
                                @elseif($setting->setting_type === 'number')
                                    <strong class="text-primary">{{ $setting->setting_value }}</strong>
                                @else
                                    {{ Str::limit($setting->setting_value, 50) }}
                                @endif
                            </td>
                            <td>
                                <small>{{ $setting->description ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $setting->setting_type }}</span>
                            </td>
                            <td>
                                @if($setting->is_public)
                                    <i class="fas fa-check text-success"></i>
                                @else
                                    <i class="fas fa-times text-danger"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">{{ __('settings.no_settings') }}</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
