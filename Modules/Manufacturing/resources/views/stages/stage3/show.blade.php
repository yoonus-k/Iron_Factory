@extends('master')

@section('title', __('stages.stage3_show_title'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ __('stages.stage3_coil_label') }} {{ $stage3->coil_number ?? 'COIL-001' }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                                    <line x1="12" y1="22.7" x2="12" y2="12"></line>
                                </svg>
                                {{ __('stages.stage3_title') }}
                            </span>
                            <span class="badge active">{{ __('stages.status_completed') }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.stage3.edit', $stage3->id ?? 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        {{ __('stages.edit_label') }}
                    </a>
                    <a href="{{ route('manufacturing.stage3.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('stages.back_label') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                            <line x1="12" y1="22.7" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('stages.stage3_coil_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            {{ __('stages.stage3_coil_number_label') }}
                        </div>
                        <div class="info-value"><span class="badge badge-info">{{ $stage3->coil_number ?? 'COIL-001' }}</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.stage3_base_weight_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->input_weight ?? 245 }} {{ __('stages.unit_kg') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8l-8 8"></path>
                            </svg>
                            {{ __('stages.stage3_dye_color_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->color ?? 'Red' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h18M3 6h18M3 18h18"></path>
                            </svg>
                            {{ __('stages.stage3_plastic_type_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->plastic_type ?? 'PE' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.stage3_dye_weight_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->dye_weight ?? 15 }} {{ __('stages.unit_kg') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.stage3_plastic_weight_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->plastic_weight ?? 20 }} {{ __('stages.unit_kg') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.stage3_total_weight_label') }}
                        </div>
                        <div class="info-value"><strong class="text-success">{{ $stage3->total_weight ?? 250 }} {{ __('stages.unit_kg') }}</strong></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ __('stages.stage3_waste_percentage_label') }}
                        </div>
                        <div class="info-value"><span class="badge badge-warning">{{ $stage3->waste_percentage ?? '3.5' }}%</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('stages.stage3_additional_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('stages.status') }}
                        </div>
                        <div class="info-value">
                            <span class="status active">{{ __('stages.status_completed') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            {{ __('stages.stage3_created_at_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->created_at->format('Y-m-d H:i') ?? '2025-01-15 09:00' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            {{ __('stages.stage3_updated_at_label') }}
                        </div>
                        <div class="info-value">{{ $stage3->updated_at->format('Y-m-d H:i') ?? '2025-01-15 15:30' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            &nbsp;
                        </div>
                        <div class="info-value">&nbsp;</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('stages.stage3_notes_label') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-value">{{ $stage3->notes ?? __('stages.stage3_coil_ready_for_next_stage') }}</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('stages.stage3_activity_log') }}</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-grid">
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.stage3_created_label') }}:</div>
                            <div class="info-value">{{ $stage3->created_at->format('Y-m-d H:i') ?? '2025-01-15 09:00' }} - {{ __('stages.stage3_by_user') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.stage3_updated_label') }}:</div>
                            <div class="info-value">{{ $stage3->updated_at->format('Y-m-d H:i') ?? '2025-01-15 12:30' }} - {{ __('stages.stage3_weight_updated') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.stage3_completed_label') }}:</div>
                            <div class="info-value">{{ $stage3->completed_at->format('Y-m-d H:i') ?? '2025-01-15 15:30' }} - {{ __('stages.stage3_moved_to_stage4') }}</div>
                        </div>
                    </div>
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
                <h3 class="card-title">{{ __('stages.available_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    <a href="{{ route('manufacturing.stage2.index') }}" class="action-btn activate" style="background: linear-gradient(135deg, #9E9E9E, #757575);">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>⬅️ {{ __('stages.back_to_stage2') }}</h4>
                            <p>{{ __('stages.back_to_stage2_desc') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('manufacturing.stage3.edit', $stage3->id ?? 1) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>{{ __('stages.edit_coil') }}</h4>
                            <p>{{ __('stages.edit_coil_details') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('manufacturing.stage4.create') }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                              <h4>➡️ {{ __('stages.move_to_stage4') }}</h4>
                            <p>{{ __('stages.move_to_stage4_desc') }}</p>
                        </div>
                    </a>
                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>{{ __('stages.delete_coil') }}</h4>
                            <p>{{ __('stages.delete_coil_desc') }}</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation to Next Stage -->
        <div class="card" style="margin-top: 20px; background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border-left: 5px solid #4CAF50;">
            <div class="card-header" style="border-bottom: 2px solid #4CAF50;">
                <h3 class="card-title" style="color: #2e7d32;">📌 {{ __('stages.next_step') }}</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #2e7d32;">{{ __('stages.stage4_packaging') }}</h4>
                        <p style="margin: 0; color: #558b2f; font-size: 14px;">{{ __('stages.stage4_packaging_desc') }}</p>
                    </div>
                    <a href="{{ route('manufacturing.stage4.create') }}" style="padding: 12px 24px; background: #4CAF50; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(76, 175, 80, 0.3);">
                        <span>➡️</span>
                        <span>{{ __('stages.move_to_stage4') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ {{ __('stages.stage3_confirm_remove_lafaf') }}\n\n{{ __('stages.delete_confirmation_warning') }}')) {
                        alert('{{ __('stages.deleted_successfully') }}');
                    }
                });
            });
        });
    </script>
@endsection
