@extends('master')

@section('title', __('stages.stage2_show_title'))

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
                        <h1>{{ __('stages.stage2_details') }} PR-001</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                                    <line x1="12" y1="22.7" x2="12" y2="12"></line>
                                </svg>
                                {{ __('stages.stage2_phase') }}
                            </span>
                            <span class="badge active">{{ __('stages.stage2_processing_status_badge') }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.stage2.edit', $stage2->id ?? 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        {{ __('stages.edit_processing') }}
                    </a>
                    <a href="{{ route('manufacturing.stage2.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('stages.back_to_list') }}
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
                    <h3 class="card-title">{{ __('stages.stage2_processing_info') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            {{ __('stages.stand_label') }}
                        </div>
                        <div class="info-value"><span class="badge badge-info">ST-001</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.input_weight_show_label') }}
                        </div>
                        <div class="info-value">250 {{ __('stages.kilogram_unit') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            {{ __('stages.output_weight_show_label') }}
                        </div>
                        <div class="info-value">245 {{ __('stages.kilogram_unit') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8l-8 8"></path>
                            </svg>
                            {{ __('stages.waste_percentage_show_label') }}
                        </div>
                        <div class="info-value"><span class="text-danger">2%</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ __('stages.waste_weight_show_label') }}
                        </div>
                        <div class="info-value"><span class="text-danger">5 {{ __('stages.kg_unit') }}</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            {{ __('stages.processing_type_show_label') }}
                        </div>
                        <div class="info-value">{{ __('stages.process_heating') }}</div>
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
                    <h3 class="card-title">{{ __('stages.stage2_additional_info') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('stages.processing_status_label') }}
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
                            {{ __('stages.created_at_label') }}
                        </div>
                        <div class="info-value">2025-01-15 09:00</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            {{ __('stages.updated_at_label') }}
                        </div>
                        <div class="info-value">2025-01-15 15:30</div>
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
                    <h3 class="card-title">{{ __('stages.notes_label_show') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-value">{{ __('stages.operation_label') }}</div>
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
                    <h3 class="card-title">{{ __('stages.activity_log_title') }}</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-grid">
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.created_by_label') }}:</div>
                            <div class="info-value">2025-01-15 09:00 - {{ __('stages.created_by_label') }} أحمد محمد</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.updated_by_label') }}:</div>
                            <div class="info-value">2025-01-15 12:30 - {{ __('stages.updated_at_label') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ __('stages.completed_by_label') }}:</div>
                            <div class="info-value">2025-01-15 15:30 - {{ __('stages.move_to_stage3') }}</div>
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
                    <a href="{{ route('manufacturing.stage1.index') }}" class="action-btn activate" style="background: linear-gradient(135deg, #9E9E9E, #757575);">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>{{ __('stages.back_to_stage1') }}</h4>
                            <p>{{ __('stages.back_to_stage1_description') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('manufacturing.stage2.edit', $stage2->id ?? 1) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>{{ __('stages.edit_processing_action') }}</h4>
                            <p>{{ __('stages.edit_processing_description') }}</p>
                        </div>
                    </a>

                   <a href="{{ route('manufacturing.stage2.create') }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                              <h4>{{ __('stages.move_to_stage3') }}</h4>
                            <p>{{ __('stages.move_to_stage3_description') }}</p>
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
                            <h4>{{ __('stages.delete_processing') }}</h4>
                            <p>{{ __('stages.delete_processing_description') }}</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation to Next Stage -->
        <div class="card" style="margin-top: 20px; background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border-left: 5px solid #4CAF50;">
            <div class="card-header" style="border-bottom: 2px solid #4CAF50;">
                <h3 class="card-title" style="color: #2e7d32;">📌 {{ __('stages.next_step_title') }}</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #2e7d32;">{{ __('stages.next_step_description') }}</h4>
                        <p style="margin: 0; color: #558b2f; font-size: 14px;">{{ __('stages.next_step_subtitle') }}</p>
                    </div>
                    <a href="{{ route('manufacturing.stage3.create') }}" style="padding: 12px 24px; background: #4CAF50; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(76, 175, 80, 0.3);">
                        <span>➡️</span>
                        <span>{{ __('stages.move_to_stage3') }}</span>
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
                    if (confirm('{{ __("stages.delete_confirmation") }}\n\n{{ __("stages.delete_confirmation_warning") }}')) {
                        alert('{{ __("stages.deleted_successfully") }}');
                    }
                });
            });
        });
    </script>
@endsection
