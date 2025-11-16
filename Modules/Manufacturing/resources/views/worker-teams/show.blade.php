@extends('master')

@section('title', 'تفاصيل المجموعة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-users"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $team->name }} - {{ $team->team_code }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                مجموعة عمال
                            </span>
                            <span class="badge {{ $team->is_active ? 'active' : 'inactive' }}">
                                {{ $team->status_name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    @if($team->is_active)
                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="btn btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            تعديل
                        </a>
                    @endif
                    <a href="{{ route('manufacturing.worker-teams.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المجموعة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            </svg>
                            رقم المجموعة
                        </div>
                        <div class="info-value">{{ $team->team_code }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            اسم المجموعة
                        </div>
                        <div class="info-value">{{ $team->name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            عدد العمال
                        </div>
                        <div class="info-value">{{ $team->workers_count }} عامل</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            حالة المجموعة
                        </div>
                        <div class="info-value">
                            <span class="status {{ $team->is_active ? 'active' : 'inactive' }}">{{ $team->status_name }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            تاريخ الإنشاء
                        </div>
                        <div class="info-value">{{ $team->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($team->description)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </div>
                        <h3 class="card-title">الوصف</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-value">{{ $team->description }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">العمال في المجموعة ({{ $workers->count() }})</h3>
                </div>
                <div class="card-body">
                    @if($workers->count() > 0)
                        <div class="workers-list">
                            @foreach($workers as $worker)
                                <div class="worker-item">
                                    <div class="worker-info">
                                        <div class="worker-avatar">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <div class="worker-details">
                                            <h4>{{ $worker->name }}</h4>
                                            <p>{{ $worker->email ?? 'لا يوجد بريد' }}</p>
                                        </div>
                                    </div>
                                    <div class="worker-status">
                                        <span class="status active">عضو</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="text-align: center; color: #999;">لا يوجد عمال في هذه المجموعة</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">الإجراءات المتاحة</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    @if($team->is_active)
                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="action-btn activate">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>تعديل المجموعة</h4>
                                <p>تعديل العمال أو معلومات المجموعة</p>
                            </div>
                        </a>

                        <form action="{{ route('manufacturing.worker-teams.toggle-status', $team->id) }}" method="POST" style="display: inline-block; width: 100%;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn delete" style="width: 100%; text-align: right;">
                                <div class="action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                                <div class="action-text">
                                    <h4>تعطيل المجموعة</h4>
                                    <p>إيقاف استخدام هذه المجموعة مؤقتاً</p>
                                </div>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('manufacturing.worker-teams.toggle-status', $team->id) }}" method="POST" style="display: inline-block; width: 100%;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn activate" style="width: 100%; text-align: right;">
                                <div class="action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div class="action-text">
                                    <h4>تفعيل المجموعة</h4>
                                    <p>إعادة تفعيل المجموعة للاستخدام</p>
                                </div>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
