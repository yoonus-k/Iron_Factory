@extends('master')

@section('title', 'مجموعات العمال')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}?v={{ time() }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-users"></i>
                    </div>
                    <div class="header-info">
                        <h1>مجموعات العمال</h1>
                        <p>إدارة مجموعات العمال للورديات المختلفة</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.worker-teams.create') }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        إضافة مجموعة جديدة
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid">
            @forelse($teams as $team)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3 class="card-title">{{ $team->name }}</h3>
                        <div class="card-badge">
                            @if($team->is_active)
                                <span class="badge active">نشطة</span>
                            @else
                                <span class="badge inactive">غير نشطة</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                </svg>
                                رقم المجموعة
                            </div>
                            <div class="info-value">{{ $team->team_code }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                                عدد العمال
                            </div>
                            <div class="info-value">
                                <span class="count-badge">{{ $team->workers_count }}</span> عامل
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                </svg>
                                تاريخ الإنشاء
                            </div>
                            <div class="info-value">{{ $team->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="btn-footer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            عرض التفاصيل
                        </a>
                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="btn-footer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            تعديل
                        </a>
                        <form action="{{ route('manufacturing.worker-teams.toggle-status', $team->id) }}" 
                              method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-footer {{ $team->is_active ? 'btn-warning' : 'btn-success' }}">
                                @if($team->is_active)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    تعطيل
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    تفعيل
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state-full">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="60" height="60">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>لا توجد مجموعات بعد</h3>
                    <p>ابدأ بإنشاء مجموعة عمال لتسهيل إدارة الورديات</p>
                    <a href="{{ route('manufacturing.worker-teams.create') }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        إضافة مجموعة جديدة
                    </a>
                </div>
            @endforelse
        </div>

        @if($teams->hasPages())
            <div class="pagination-wrapper">
                {{ $teams->links() }}
            </div>
        @endif
    </div>

    <style>
        .count-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
        }

        .empty-state-full {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin-bottom: 30px;
        }

        .empty-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
        }

        .empty-state-full h3 {
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .empty-state-full p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn-footer.btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-footer.btn-warning:hover {
            background: #d97706;
        }

        .btn-footer.btn-success {
            background: #10b981;
            color: white;
        }

        .btn-footer.btn-success:hover {
            background: #059669;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert i {
            font-size: 20px;
        }

        /* Improve consistency of view and edit buttons */
        .btn-footer {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-footer svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .btn-footer:first-child {
            background: #3b82f6;
            color: white;
        }

        .btn-footer:first-child:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .btn-footer:nth-child(2) {
            background: #6b7280;
            color: white;
        }

        .btn-footer:nth-child(2):hover {
            background: #4b5563;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(107, 114, 128, 0.3);
        }
    </style>
@endsection