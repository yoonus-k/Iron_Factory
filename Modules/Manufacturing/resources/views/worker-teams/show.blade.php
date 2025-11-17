@extends('master')

@section('title', 'تفاصيل المجموعة')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                {{ $team->name }} - {{ $team->team_code }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>مجموعات العمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>تفاصيل المجموعة</span>
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
                    <i class="feather icon-info"></i>
                    معلومات المجموعة
                </h4>
                <div style="display: flex; gap: 10px;">
                    @if($team->is_active)
                    <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="um-btn um-btn-secondary">
                        <i class="feather icon-edit-2"></i>
                        تعديل
                    </a>
                    @endif
                    <a href="{{ route('manufacturing.worker-teams.index') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i>
                        العودة
                    </a>
                </div>
            </div>

            <!-- Team Information -->
            <div style="padding: 20px;">
                <div class="um-category-card" style="margin-bottom: 20px;">
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
                            {{ $team->status_name }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">
                                <i class="feather icon-hash"></i>
                                رقم المجموعة
                            </span>
                            <span class="um-info-value">{{ $team->team_code }}</span>
                        </div>

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
                            <span class="um-info-value">{{ $team->created_at->format('Y-m-d H:i') }}</span>
                        </div>

                        @if($team->description)
                        <div class="um-info-row">
                            <span class="um-info-label">
                                <i class="feather icon-file-text"></i>
                                الوصف
                            </span>
                            <span class="um-info-value">{{ $team->description }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Workers in Team -->
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #f59e0b20; color: #f59e0b; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-user" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">العمال في المجموعة</h6>
                                <span class="um-category-id">{{ $workers->count() }} عامل</span>
                            </div>
                        </div>
                    </div>

                    <div class="um-category-card-body">
                        @if($workers->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                            @foreach($workers as $worker)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div style="background: #667eea20; color: #667eea; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                    <i class="feather icon-user" style="font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #0f172a;">{{ $worker->name }}</div>
                                    <div style="font-size: 0.85rem; color: #64748b;">{{ $worker->email ?? 'لا يوجد بريد' }}</div>
                                </div>
                                <span class="um-badge um-badge-success" style="margin-right: auto;">عضو</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div style="text-align: center; padding: 40px; color: #999;">
                            <i class="feather icon-users" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                            لا يوجد عمال في هذه المجموعة
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
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
            text-align: left;
        }

        @media (max-width: 768px) {
            .um-info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .um-info-value {
                text-align: right;
                width: 100%;
            }
        }
    </style>
@endsection