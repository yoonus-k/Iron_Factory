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

    <!-- قسم نسب الهدر للمراحل -->
    @php
        $wasteSettings = $settings['waste_limits'] ?? collect();
        $stageWasteSettings = $wasteSettings->whereIn('setting_key', [
            'stage1_waste_percentage',
            'stage2_waste_percentage',
            'stage3_waste_percentage',
            'stage4_waste_percentage'
        ])->keyBy('setting_key');
    @endphp

    @if($stageWasteSettings->isNotEmpty())
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-industry me-2"></i>
                نسب الهدر المسموح بها في المراحل الإنتاجية
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- المرحلة الأولى: الاستاندات -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-cut fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">المرحلة الأولى</h6>
                            <p class="text-muted small mb-2">الاستاندات</p>
                            <div class="display-6 text-primary fw-bold">
                                {{ $stageWasteSettings->get('stage1_waste_percentage')->setting_value ?? '3' }}%
                            </div>
                            <small class="text-muted">نسبة الهدر المسموح بها</small>
                        </div>
                    </div>
                </div>

                <!-- المرحلة الثانية: المعالجة -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-cogs fa-2x text-success mb-3"></i>
                            <h6 class="card-title">المرحلة الثانية</h6>
                            <p class="text-muted small mb-2">المعالجة</p>
                            <div class="display-6 text-success fw-bold">
                                {{ $stageWasteSettings->get('stage2_waste_percentage')->setting_value ?? '3' }}%
                            </div>
                            <small class="text-muted">نسبة الهدر المسموح بها</small>
                        </div>
                    </div>
                </div>

                <!-- المرحلة الثالثة: اللفائف -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-codiepie fa-2x text-info mb-3"></i>
                            <h6 class="card-title">المرحلة الثالثة</h6>
                            <p class="text-muted small mb-2">اللفائف</p>
                            <div class="display-6 text-info fw-bold">
                                {{ $stageWasteSettings->get('stage3_waste_percentage')->setting_value ?? '3' }}%
                            </div>
                            <small class="text-muted">نسبة الهدر المسموح بها</small>
                        </div>
                    </div>
                </div>

                <!-- المرحلة الرابعة: التعبئة -->
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-box-open fa-2x text-danger mb-3"></i>
                            <h6 class="card-title">المرحلة الرابعة</h6>
                            <p class="text-muted small mb-2">التعبئة</p>
                            <div class="display-6 text-danger fw-bold">
                                {{ $stageWasteSettings->get('stage4_waste_percentage')->setting_value ?? '3' }}%
                            </div>
                            <small class="text-muted">نسبة الهدر المسموح بها</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>ملاحظة:</strong> هذه النسب تمثل الحد الأقصى المسموح به للهدر في كل مرحلة. عند تجاوز هذه النسبة، سيتم إيقاف المرحلة مؤقتاً حتى تتم الموافقة من المشرف.
            </div>
        </div>
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
