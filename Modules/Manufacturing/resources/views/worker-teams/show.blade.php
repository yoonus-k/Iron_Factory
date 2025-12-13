@extends('master')

@section('title', __('shifts-workers.team_details') . ' - ' . $team->name)

@section('content')
    <style>
        .action-btn.status {
            display: flex;
            align-items: center;
            color: #0066cc;
            border: none;
        }
        .action-btn.status:hover {
            color: #004499;
        }

        .info-item {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-right: 4px solid #3498db;
        }

        .info-item label {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 5px;
            font-weight: 600;
            display: block;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .card-icon.primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .card-icon.success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .card-icon.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .card-body {
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-info h1 {
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }

        .header-info p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .team-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
        }

        .btn-back {
            background: white;
            border: 1px solid #e9ecef;
            color: #2c3e50;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #f8f9fa;
            border-color: #3498db;
            color: #3498db;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .col-12 {
            grid-column: 1 / -1;
        }

        .worker-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .worker-item:last-child {
            margin-bottom: 0;
        }

        .worker-icon {
            background: #667eea20;
            color: #667eea;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .worker-info {
            flex: 1;
        }

        .worker-name {
            font-weight: 500;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .worker-email {
            font-size: 0.85rem;
            color: #64748b;
        }

        .worker-badge {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            opacity: 0.5;
        }
    </style>

    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="team-icon">
                        <i class="feather icon-users"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $team->name }}</h1>
                        <p><strong>{{ __('shifts-workers.team_code_label') }}:</strong> {{ $team->team_code }}</p>
                        @if($team->manager)
                        <p style="margin: 8px 0 0 0; color: #3498db;">
                            <strong>المسؤول:</strong> {{ $team->manager->name }}
                        </p>
                        @endif

                    </div>
                </div>
                <div class="header-actions">
                    @can('WORKER_TEAMS_UPDATE')
                    <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="action-btn view">
                        <i class="feather icon-edit-2"></i> {{ __('shifts-workers.edit') }}
                    </a>
                    @endcan
                    @can('WORKER_TEAMS_READ')
                    <a href="{{ route('manufacturing.worker-teams.index') }}" class="btn-back">
                        <i class="feather icon-arrow-right"></i> {{ __('shifts-workers.back') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="feather icon-info"></i>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.team_code_label') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>{{ __('shifts-workers.team_name_label') }}</label>
                        <div class="value">{{ $team->name }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.team_code_label') }}</label>
                        <div class="value"><code style="background: white; padding: 6px 10px; border-radius: 4px;">{{ $team->team_code }}</code></div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.workers_count') }}</label>
                        <div class="value">
                            <span class="status-badge active">{{ $team->workers_count }} {{ __('shifts-workers.worker') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.created_date') }}</label>
                        <div class="value">{{ $team->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.description_label') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>{{ __('shifts-workers.description_label') }}</label>
                        <div class="value">{{ $team->description ?? __('shifts-workers.not_specified') }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.status') }}</label>
                        <div class="value">
                            <span class="status-badge {{ $team->is_active ? 'active' : 'inactive' }}">
                                {{ $team->is_active ? __('shifts-workers.active') : __('shifts-workers.inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="feather icon-briefcase"></i>
                    </div>
                    <h3 class="card-title">المسؤول عن المجموعة</h3>
                </div>
                <div class="card-body">
                    @if($team->manager)
                    <div class="info-item">
                        <label>اسم المسؤول</label>
                        <div class="value">{{ $team->manager->name }}</div>
                    </div>

                    <div class="info-item">
                        <label>البريد الإلكتروني</label>
                        <div class="value">{{ $team->manager->email ?? __('shifts-workers.not_specified') }}</div>
                    </div>

                    <div class="info-item">
                        <label>الهاتف</label>
                        <div class="value">{{ $team->manager->phone ?? __('shifts-workers.not_specified') }}</div>
                    </div>
                    @else
                    <div class="empty-state" style="padding: 20px;">
                        <p style="margin: 0; color: #7f8c8d;">لا يوجد مسؤول معين</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Workers in Team -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <i class="feather icon-user"></i>
                        </div>
                        <h3 class="card-title">{{ __('shifts-workers.workers_label') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($workers->count() > 0)
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                                @foreach($workers as $worker)
                                    <div class="worker-item">
                                        <div class="worker-icon">
                                            <i class="feather icon-user" style="font-size: 16px;"></i>
                                        </div>
                                        <div class="worker-info">
                                            <div class="worker-name">{{ $worker->name }}</div>
                                            <div class="worker-email">{{ $worker->email ?? __('shifts-workers.not_specified') }}</div>
                                        </div>
                                        <span class="worker-badge">{{ __('shifts-workers.worker') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="feather icon-users"></i>
                                <p><strong>{{ __('shifts-workers.no_workers') }}</strong></p>
                                <small>{{ __('shifts-workers.no_workers') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-info)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
@endsection
