@extends('master')

@section('title', 'مجموعات العمال')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                مجموعات العمال
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>مجموعات العمال</span>
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

        <!-- Statistics Cards -->
        <div class="um-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['total'] }}</div>
                <div style="opacity: 0.9;">إجمالي المجموعات</div>
            </div>
            <div class="um-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['active'] }}</div>
                <div style="opacity: 0.9;">مجموعات نشطة</div>
            </div>
            <div class="um-stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['workers'] }}</div>
                <div style="opacity: 0.9;">إجمالي العمال</div>
            </div>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة مجموعات العمال
                </h4>
                <a href="{{ route('manufacturing.worker-teams.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة مجموعة جديدة
                </a>
            </div>

            <!-- Teams Grid -->
            <div class="um-teams-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
                @forelse($teams as $team)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #667eea20; color: #667eea; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $team->name }}</h6>
                                <span class="um-category-id">{{ $team->team_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-{{ $team->is_active ? 'success' : 'secondary' }}">
                            {{ $team->is_active ? 'نشطة' : 'غير نشطة' }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">
                                <i class="feather icon-user"></i>
                                عدد العمال
                            </span>
                            <span class="um-info-value">
                                <span class="count-badge">{{ $team->workers_count }}</span> عامل
                            </span>
                        </div>

                        <div class="um-info-row">
                            <span class="um-info-label">
                                <i class="feather icon-calendar"></i>
                                تاريخ الإنشاء
                            </span>
                            <span class="um-info-value">{{ $team->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض التفاصيل
                        </a>
                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                        <form action="{{ route('manufacturing.worker-teams.toggle-status', $team->id) }}" 
                              method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="um-btn um-btn-sm {{ $team->is_active ? 'um-btn-warning' : 'um-btn-success' }}">
                                @if($team->is_active)
                                    <i class="feather icon-pause-circle" style="font-size: 14px;"></i>
                                    تعطيل
                                @else
                                    <i class="feather icon-play-circle" style="font-size: 14px;"></i>
                                    تفعيل
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin-bottom: 30px;">
                        <i class="feather icon-users" style="font-size: 60px; color: white;"></i>
                    </div>
                    <h3 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">لا توجد مجموعات بعد</h3>
                    <p style="color: #64748b; font-size: 16px; margin-bottom: 30px;">ابدأ بإنشاء مجموعة عمال لتسهيل إدارة الورديات</p>
                    <a href="{{ route('manufacturing.worker-teams.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        إضافة مجموعة جديدة
                    </a>
                </div>
                @endforelse
            </div>

            @if($teams->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $teams->firstItem() ?? 0 }} إلى {{ $teams->lastItem() ?? 0 }} من أصل {{ $teams->total() }} مجموعة
                    </p>
                </div>
                <div>
                    {{ $teams->links() }}
                </div>
            </div>
            @endif
        </section>
    </div>

    <style>
        .count-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
        }

        .um-category-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .um-category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .um-category-card-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .um-category-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .um-category-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 4px 0;
        }

        .um-category-id {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .um-category-card-body {
            padding: 1.25rem;
        }

        .um-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .um-info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .um-info-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .um-info-value {
            font-weight: 500;
            color: #0f172a;
        }

        .um-category-card-footer {
            padding: 1.25rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .um-btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
        }

        .um-btn-secondary {
            background: #6b7280;
            color: white;
            border: none;
        }

        .um-btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(107, 114, 128, 0.3);
        }

        .um-btn-warning {
            background: #f59e0b;
            color: white;
            border: none;
        }

        .um-btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        .um-btn-success {
            background: #10b981;
            color: white;
            border: none;
        }

        .um-btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }

        .um-teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .um-teams-grid {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            
            .um-category-card-footer {
                flex-direction: column;
            }
            
            .um-btn-sm {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
@endsection