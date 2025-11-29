@extends('master')

@section('title', __('worker-teams.edit_team'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
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

        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            {{ __('worker-teams.edit_team') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('worker-teams.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('worker-teams.worker_teams') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('worker-teams.edit_team') }}</span>
        </nav>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.worker-teams.update', $team->id) }}" id="teamForm">
            @csrf
            @method('PUT')

            <!-- Team Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('worker-teams.team_code_label') }}</h3>
                        <p class="section-subtitle">{{ __('worker-teams.team_updated_successfully') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="team_code" class="form-label">
                            {{ __('worker-teams.team_code_label') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-group-with-button">
                            <div class="input-wrapper" style="flex: 1;">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                </svg>
                                <input type="text" name="team_code" id="team_code"
                                    class="form-input @error('team_code') is-invalid @enderror"
                                    value="{{ old('team_code', $team->team_code) }}" placeholder="{{ __('worker-teams.team_code_label') }}" required readonly>
                            </div>
                        </div>
                        @error('team_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">
                            {{ __('worker-teams.team_name_label') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" id="name"
                                class="form-input @error('name') is-invalid @enderror"
                                value="{{ old('name', $team->name) }}" placeholder="{{ __('worker-teams.team_name_label') }}" required>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">{{ __('worker-teams.description_label') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <textarea name="description" id="description" rows="3"
                                class="form-input @error('description') is-invalid @enderror"
                                placeholder="{{ __('worker-teams.description_label') }}">{{ old('description', $team->description) }}</textarea>
                        </div>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Worker Selection -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon account">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('worker-teams.workers_label') }}</h3>
                        <p class="section-subtitle">{{ __('worker-teams.description_label') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <div class="workers-selection-header">
                            <label class="form-label">{{ __('worker-teams.workers_label') }}</label>
                            <div class="selection-actions">
                                <button type="button" class="btn-select-all">{{ __('worker-teams.select_all') }}</button>
                                <button type="button" class="btn-deselect-all">{{ __('worker-teams.deselect_all') }}</button>
                                <span class="selected-count">{{ __('worker-teams.showing') }}: <strong id="selectedCount">{{ count(old('workers', $team->worker_ids ?? [])) }}</strong></span>
                            </div>
                        </div>

                        <div class="workers-selection" style="max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                            @forelse($workers as $worker)
                                <div class="worker-item">
                                    <input type="checkbox"
                                           id="worker_{{ $worker->id }}"
                                           name="workers[]"
                                           value="{{ $worker->id }}"
                                           {{ in_array($worker->id, old('workers', $team->worker_ids ?? [])) ? 'checked' : '' }}
                                           class="worker-checkbox">
                                    <label for="worker_{{ $worker->id }}">
                                        {{ $worker->name }} - {{ $worker->email ?? __('worker-teams.not_specified') }}
                                    </label>
                                </div>
                            @empty
                                <p style="color: #999; text-align: center;">{{ __('worker-teams.no_workers') }}</p>
                            @endforelse
                        </div>
                        @error('workers')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                @if(auth()->user()->hasPermission('WORKER_TEAMS_UPDATE'))
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ __('worker-teams.update') }}
                </button>
                @endif
                @if(auth()->user()->hasPermission('WORKER_TEAMS_READ'))
                <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    {{ __('worker-teams.cancel') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    <style>
        .input-group-with-button {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
            min-height: 48px;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.35);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-generate:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
        }

        .btn-generate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-generate svg {
            width: 18px;
            height: 18px;
            animation: spin 0s linear infinite;
        }

        .btn-generate.loading svg {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .btn-generate.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-generate.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .workers-selection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .selection-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-select-all, .btn-deselect-all {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-select-all:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .btn-deselect-all:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .selected-count {
            font-size: 13px;
            color: #64748b;
        }

        .selected-count strong {
            color: #3b82f6;
            font-size: 15px;
        }

        .worker-item {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }

        .worker-item:hover {
            background: #f8fafc;
        }

        .worker-item:last-child {
            border-bottom: none;
        }

        .worker-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .worker-item label {
            cursor: pointer;
            flex: 1;
            margin: 0;
            font-size: 14px;
            color: #1e293b;
        }

        /* Reduce icon sizes */
        .section-icon svg {
            width: 20px;
            height: 20px;
        }

        .input-icon {
            width: 18px;
            height: 18px;
        }

        .title-icon {
            width: 24px;
            height: 24px;
        }

        @media (max-width: 768px) {
            .input-group-with-button {
                flex-direction: column;
            }

            .btn-generate {
                width: 100%;
                justify-content: center;
            }

            .section-icon svg {
                width: 18px;
                height: 18px;
            }

            .input-icon {
                width: 16px;
                height: 16px;
            }
        }
    </style>

    <script>
        // Select/Deselect All
        document.querySelector('.btn-select-all').addEventListener('click', function() {
            document.querySelectorAll('.worker-checkbox').forEach(cb => cb.checked = true);
            updateSelectedCount();
        });

        document.querySelector('.btn-deselect-all').addEventListener('click', function() {
            document.querySelectorAll('.worker-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        });

        // Update selected workers count
        function updateSelectedCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Update count on page load
        updateSelectedCount();
    </script>
@endsection
