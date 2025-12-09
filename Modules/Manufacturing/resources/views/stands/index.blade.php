@extends('master')

@section('title', __('stands.title.index'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                {{ __('stands.header.manage_stands') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('stands.breadcrumb.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('stands.breadcrumb.stands') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('stands.card.stands_list') }}
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.stands.usage-history') }}" class="um-btn um-btn-info">
                        <i class="feather icon-clock"></i>
                        {{ __('stands.btn.usage_history') }}
                    </a>
                    <a href="{{ route('manufacturing.stands.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        {{ __('stands.btn.add_new') }}
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('stands.placeholder.search') }}" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('stands.filter.all_statuses') }}</option>
                                <option value="unused" {{ request('status') == 'unused' ? 'selected' : '' }}>{{ __('stands.status.unused') }}</option>
                                <option value="stage1" {{ request('status') == 'stage1' ? 'selected' : '' }}>{{ __('stands.status.stage1') }}</option>
                                <option value="stage2" {{ request('status') == 'stage2' ? 'selected' : '' }}>{{ __('stands.status.stage2') }}</option>
                                <option value="stage3" {{ request('status') == 'stage3' ? 'selected' : '' }}>{{ __('stands.status.stage3') }}</option>
                                <option value="stage4" {{ request('status') == 'stage4' ? 'selected' : '' }}>{{ __('stands.status.stage4') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('stands.status.completed') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="{{ __('stands.filter.date') }}" value="{{ request('date') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('stands.btn.search') }}
                            </button>
                            <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('stands.btn.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('stands.form.stand_number') }}</th>
                            <th>{{ __('stands.form.weight') }}</th>
                            <th>{{ __('stands.form.status') }}</th>
                            <th>{{ __('stands.form.created_at') }}</th>
                            <th>{{ __('stands.form.is_active') }}</th>
                            <th>{{ __('stands.breadcrumb.stands') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stands as $stand)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $stand->stand_number }}</strong></td>
                                <td>{{ number_format($stand->weight, 2) }} كجم</td>
                                <td>
                                    <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                                </td>
                                <td>{{ $stand->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($stand->is_active)
                                        <span class="um-badge um-badge-success">نشط</span>
                                    @else
                                        <span class="um-badge um-badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-action um-btn-dropdown" title="{{ __('stands.card.stands_list') }}">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="um-dropdown-menu">
                                            <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>{{ __('stands.btn.view') }}</span>
                                            </a>
                                            <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>{{ __('stands.btn.edit') }}</span>
                                            </a>
                                            <form action="{{ route('manufacturing.stands.toggle-status', $stand->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                    <i class="feather icon-{{ $stand->is_active ? 'pause' : 'play' }}-circle"></i>
                                                    <span>{{ $stand->is_active ? __('stands.btn.disable') : __('stands.btn.enable') }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('manufacturing.stands.destroy', $stand->id) }}" style="display: inline;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-dropdown-item um-btn-delete">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>{{ __('stands.btn.delete') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px; color: #999;">
                                    <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                    {{ __('stands.message.no_stands') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($stands as $stand)
                    <div class="um-category-card">
                        <div class="um-category-card-header">
                            <div class="um-category-info">
                                <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                    <i class="feather icon-package"></i>
                                </div>
                                <div>
                                    <h6 class="um-category-name">{{ $stand->stand_number }}</h6>
                                    <span class="um-category-id">{{ __('stands.form.weight') }}: {{ number_format($stand->weight, 2) }} {{ __('stands.info.weight_unit') }}</span>
                                </div>
                            </div>
                            <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                        </div>

                        <div class="um-category-card-body">
                            <div class="um-info-row">
                                <span class="um-info-label">{{ __('stands.form.created_at') }}:</span>
                                <span class="um-info-value">{{ $stand->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="um-info-row">
                                <span class="um-info-label">{{ __('stands.form.is_active') }}:</span>
                                <span class="um-info-value">
                                    @if($stand->is_active)
                                        <span class="um-badge um-badge-success">{{ __('stands.active') }}</span>
                                    @else
                                        <span class="um-badge um-badge-secondary">{{ __('stands.inactive') }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($stand->notes)
                                <div class="um-info-row">
                                    <span class="um-info-label">{{ __('stands.form.notes') }}:</span>
                                    <span class="um-info-value">{{ Str::limit($stand->notes, 50) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="um-category-card-footer">
                            <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-btn um-btn-sm um-btn-outline">
                                <i class="feather icon-eye"></i> {{ __('stands.btn.view') }}
                            </a>
                            <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-sm um-btn-primary">
                                <i class="feather icon-edit-2"></i> {{ __('stands.btn.edit') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        <p>{{ __('stands.message.no_stands_mobile') }}</p>
                        <a href="{{ route('manufacturing.stands.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                            <i class="feather icon-plus"></i> {{ __('stands.btn.add_new') }}
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($stands->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('stands.info.showing') }} {{ $stands->firstItem() }} {{ __('stands.info.to') }} {{ $stands->lastItem() }} {{ __('stands.info.of') }} {{ $stands->total() }} {{ __('stands.info.stand') }}
                        </p>
                    </div>
                    <div>
                        {{ $stands->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('{{ __('stands.alert.confirm_delete') }}\n\n{{ __('stands.alert.confirm_delete_warning') }}')) {
                        this.submit();
                    }
                });
            });

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>

@endsection
