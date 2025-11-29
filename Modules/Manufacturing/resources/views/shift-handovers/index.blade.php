@extends('master')

@section('title', __('shifts-workers.shift_handovers'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-exchange-2"></i>
                {{ __('shifts-workers.shift_handovers') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('shifts-workers.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.shift_handovers') }}</span>
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

        <!-- Statistics Section -->

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('shifts-workers.handover_list') }}
                </h4>
                <div style="display: flex; gap: 10px;">

                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-arrow-left"></i>
                        {{ __('shifts-workers.back_button') }}
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.shift-handovers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <select name="approval_status" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_statuses') }}</option>
                                <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>{{ __('shifts-workers.approved') }}</option>
                                <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>{{ __('shifts-workers.pending') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="stage_number" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_stages') }}</option>
                                <option value="1" {{ request('stage_number') == '1' ? 'selected' : '' }}>{{ __('shifts-workers.stage_first') }}</option>
                                <option value="2" {{ request('stage_number') == '2' ? 'selected' : '' }}>{{ __('shifts-workers.stage_second') }}</option>
                                <option value="3" {{ request('stage_number') == '3' ? 'selected' : '' }}>{{ __('shifts-workers.stage_third') }}</option>
                                <option value="4" {{ request('stage_number') == '4' ? 'selected' : '' }}>{{ __('shifts-workers.stage_fourth') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" value="{{ request('date') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('shifts-workers.apply_filters') }}
                            </button>
                            <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('shifts-workers.clear_filters') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('shifts-workers.from_worker') }}</th>
                            <th>{{ __('shifts-workers.to_worker') }}</th>
                            <th>{{ __('shifts-workers.stage') }}</th>
                            <th>{{ __('shifts-workers.handover_time') }}</th>
                            <th>{{ __('shifts-workers.handover_status') }}</th>
                            <th>{{ __('shifts-workers.approver') }}</th>
                            <th>{{ __('workers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($handovers as $index => $handover)
                        <tr>
                            <td>{{ $handovers->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $handover->fromUser->name ?? __('shifts-workers.not_specified') }}</strong>
                            </td>
                            <td>
                                <strong style="color: #28a745;">{{ $handover->toUser->name ?? __('shifts-workers.not_specified') }}</strong>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">{{ __('shifts-workers.stage') }} {{ $handover->stage_number }}</span>
                            </td>
                            <td>{{ $handover->handover_time->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($handover->supervisor_approved)
                                    <span class="um-badge um-badge-success">{{ __('shifts-workers.approved') }}</span>
                                @else
                                    <span class="um-badge um-badge-warning">{{ __('shifts-workers.pending') }}</span>
                                @endif
                            </td>
                            <td>{{ $handover->approver->name ?? __('shifts-workers.none') }}</td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('workers.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.shift-handovers.show', $handover->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('shifts-workers.view') }}</span>
                                        </a>
                                        @if(!$handover->supervisor_approved)
                                            @if(auth()->user()->hasPermission('SHIFT_HANDOVERS_APPROVE'))
                                            <form method="POST" action="{{ route('manufacturing.shift-handovers.approve', $handover->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="um-dropdown-item um-btn-feature" onclick="return confirm('{{ __('shifts-workers.confirm_approve') }}')">
                                                    <i class="feather icon-check"></i>
                                                    <span>{{ __('shifts-workers.approve_handover') }}</span>
                                                </button>
                                            </form>
                                            @endif
                                            @if(auth()->user()->hasPermission('SHIFT_HANDOVERS_REJECT'))
                                            <button type="button" class="um-dropdown-item um-btn-delete" onclick="openRejectModal({{ $handover->id }})">
                                                <i class="feather icon-x"></i>
                                                <span>{{ __('shifts-workers.reject_handover') }}</span>
                                            </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                {{ __('shifts-workers.no_handovers_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('shifts-workers.showing') }} {{ $handovers->firstItem() ?? 0 }} {{ __('shifts-workers.to') }} {{ $handovers->lastItem() ?? 0 }} {{ __('shifts-workers.of') }} {{ $handovers->total() }} {{ __('shifts-workers.handovers_count') }}
                    </p>
                </div>
                <div>
                    {{ $handovers->links() }}
                </div>
            </div>
        </section>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

        // Reject Modal Functions
        function openRejectModal(handoverId) {
            const form = document.getElementById('rejectForm');
            form.action = `/manufacturing/shift-handovers/${handoverId}/reject`;
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

    <style>
        .um-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 0;
        }

        .um-stat-card {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .um-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

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

        @media (max-width: 768px) {
            .um-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

@endsection
