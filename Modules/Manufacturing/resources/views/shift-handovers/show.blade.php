@extends('master')

@section('title', __('shifts-workers.handover_details') . ' - #' . $handover->id)

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-exchange-2"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ __('shifts-workers.shift_handover') }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                <i class="feather icon-layers"></i>
                                {{ __('shifts-workers.stage') }} {{ $handover->stage_number }}
                            </span>
                            <span class="badge {{ $handover->supervisor_approved ? 'active' : 'scheduled' }}">
                                <i class="feather icon-{{ $handover->supervisor_approved ? 'check-circle' : 'clock' }}"></i>
                                {{ $handover->supervisor_approved ? __('shifts-workers.approved') : __('shifts-workers.pending') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('shifts-workers.back_button') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid">
            <!-- From User Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.from_user') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ __('shifts-workers.name') }}
                        </div>
                        <div class="info-value">{{ $handover->fromUser->name ?? __('shifts-workers.not_specified') }}</div>
                    </div>
                </div>
            </div>

            <!-- To User Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.to_user') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ __('shifts-workers.name') }}
                        </div>
                        <div class="info-value" style="color: #28a745;">{{ $handover->toUser->name ?? __('shifts-workers.not_specified') }}</div>
                    </div>
                </div>
            </div>

            <!-- Handover Details Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.handover_details') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            {{ __('shifts-workers.stage') }}
                        </div>
                        <div class="value">
                            <span class="status active">{{ __('shifts-workers.stage') }} {{ $handover->stage_number }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ __('shifts-workers.handover_time') }}
                        </div>
                        <div class="info-value">{{ $handover->handover_time->format('Y-m-d H:i:s') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"></path>
                            </svg>
                            {{ __('shifts-workers.handover_status') }}
                        </div>
                        <div class="info-value">
                            @if($handover->supervisor_approved)
                                <span class="status active">{{ __('shifts-workers.approved') }}</span>
                            @else
                                <span class="status scheduled">{{ __('shifts-workers.pending') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Info Card -->
            @if($handover->supervisor_approved)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.approval_info') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ __('shifts-workers.approved_by') }}
                        </div>
                        <div class="info-value">{{ $handover->approver->name ?? __('shifts-workers.none') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ __('shifts-workers.approval_date') }}
                        </div>
                        <div class="info-value">{{ $handover->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Handover Items Card -->
            @if($handover->handover_items && count($handover->handover_items) > 0)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.handover_items') }}</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($handover->handover_items as $item)
                        <div style="padding: 10px; background: #f8f9fa; border-left: 3px solid #667eea; border-radius: 4px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; margin-left: 8px; color: #667eea;"></svg>
                            {{ $item }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes Card -->
            @if($handover->notes)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.notes_section') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-value" style="background: #f8f9fa; padding: 15px; border-radius: 6px; line-height: 1.6;">
                            {{ $handover->notes }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Card -->
        @if(!$handover->supervisor_approved)
        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">{{ __('shifts-workers.available_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    @can('SHIFT_HANDOVERS_APPROVE')
                    <form action="{{ route('manufacturing.shift-handovers.approve', $handover->id) }}" method="POST" style="display: inline-block; width: 48%;">
                        @csrf
                        <button type="submit" class="action-btn activate" style="width: 100%; text-align: right;" onclick="return confirm('{{ __('shifts-workers.confirm_approve') }}');">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>{{ __('shifts-workers.approval_info') }}</h4>
                                <p>{{ __('shifts-workers.handover_approved') }}</p>
                            </div>
                        </button>
                    </form>
                    @endcan

                    @can('SHIFT_HANDOVERS_REJECT')
                    <button type="button" class="action-btn delete" onclick="openRejectModal()" style="width: 48%; text-align: right;">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>الرفض</h4>
                            <p>رفض النقل مع توضيح السبب</p>
                        </div>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <div class="card-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h3 class="card-title">{{ __('shifts-workers.handover_approved') }}</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; padding: 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 60px; height: 60px; margin: 0 auto 15px; color: #28a745;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <p style="color: #666; margin: 0;">تمت الموافقة على نقل الوردية من قبل {{ $handover->approver->name }} في {{ $handover->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('shifts-workers.reject_handover') }}</h3>
                <button class="close-btn" onclick="closeRejectModal()">×</button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">{{ __('shifts-workers.rejection_reason_label') }}</label>
                        <textarea
                            id="rejection_reason"
                            name="rejection_reason"
                            class="form-control"
                            rows="4"
                            placeholder="{{ __('shifts-workers.rejection_reason_placeholder') }}"
                            required>
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">{{ __('shifts-workers.cancel_button') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('shifts-workers.reject_button') }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .actions-grid {
            display: flex;
            gap: 20px;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            .actions-grid {
                flex-direction: column;
            }

            .action-btn {
                width: 100% !important;
            }
        }
    </style>

    <script>
        function openRejectModal() {
            const form = document.getElementById('rejectForm');
            form.action = `{{ route('manufacturing.shift-handovers.reject', $handover->id) }}`;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejection_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>

@endsection
