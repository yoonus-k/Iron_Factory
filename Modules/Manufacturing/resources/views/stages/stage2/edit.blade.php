@extends('master')

@section('title', __('stages.stage2_edit_processing'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                <line x1="12" y1="22.7" x2="12" y2="12"></line>
            </svg>
            {{ __('stages.stage2_edit_processing') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> {{ __('stages.dashboard') }}
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.stage2_title') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.edit_processing') }}</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage2.update', $stage2->id ?? 1) }}" id="stage2Form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Processing Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                            <line x1="12" y1="22.7" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('stages.stage2_processing_information') }}</h3>
                        <p class="section-subtitle">{{ __('stages.update_processing_data') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="stage1_id" class="form-label">
                            {{ __('stages.stand_number') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <select name="stage1_id" id="stage1_id" class="form-input" required>
                                <option value="">{{ __('stages.choose_stand') }}</option>
                                <option value="1" {{ ($stage2->stage1_id ?? old('stage1_id')) == '1' ? 'selected' : '' }}>ST-001</option>
                                <option value="2" {{ ($stage2->stage1_id ?? old('stage1_id')) == '2' ? 'selected' : '' }}>ST-002</option>
                                <option value="3" {{ ($stage2->stage1_id ?? old('stage1_id')) == '3' ? 'selected' : '' }}>ST-003</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input_weight" class="form-label">
                            {{ __('stages.input_weight_label') }} (كجم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="input_weight" id="input_weight" class="form-input" value="{{ $stage2->input_weight ?? old('input_weight') }}" placeholder="{{ __('stages.enter_input_weight') }}" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="output_weight" class="form-label">
                            {{ __('stages.output_weight_label') }} (كجم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="output_weight" id="output_weight" class="form-input" value="{{ $stage2->output_weight ?? old('output_weight') }}" placeholder="{{ __('stages.enter_output_weight') }}" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="process_details" class="form-label">
                            {{ __('stages.processing_type') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <select name="process_details" id="process_details" class="form-input" required>
                                <option value="">{{ __('stages.stage2_select_processing_type') }}</option>
                                <option value="heating" {{ ($stage2->process_details ?? old('process_details')) == 'heating' ? 'selected' : '' }}>{{ __('stages.process_heating') }}</option>
                                <option value="cooling" {{ ($stage2->process_details ?? old('process_details')) == 'cooling' ? 'selected' : '' }}>{{ __('stages.process_cooling') }}</option>
                                <option value="cutting" {{ ($stage2->process_details ?? old('process_details')) == 'cutting' ? 'selected' : '' }}>{{ __('stages.process_cutting') }}</option>
                                <option value="rolling" {{ ($stage2->process_details ?? old('process_details')) == 'rolling' ? 'selected' : '' }}>{{ __('stages.process_rolling') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">{{ __('stages.status') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                            <select name="status" id="status" class="form-input">
                                <option value="created" {{ ($stage2->status ?? old('status')) == 'created' ? 'selected' : '' }}>{{ __('stages.stand_status_created') }}</option>
                                <option value="in_process" {{ ($stage2->status ?? old('status')) == 'in_process' ? 'selected' : '' }}>{{ __('stages.stand_status_in_process') }}</option>
                                <option value="completed" {{ ($stage2->status ?? old('status')) == 'completed' ? 'selected' : '' }}>{{ __('stages.stand_status_completed') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="waste_percentage" class="form-label">{{ __('stages.waste_percentage') }} (%)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <input type="number" name="waste_percentage" id="waste_percentage" class="form-input" value="{{ $stage2->waste_percentage ?? old('waste_percentage') }}" placeholder="{{ __('stages.waste_percentage') }}" step="0.01" min="0" max="100">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes" class="form-label">{{ __('stages.notes_label') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="{{ __('stages.enter_notes_for_processing') }}">{{ $stage2->notes ?? old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        &nbsp;
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon account">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('stages.additional_information') }}</h3>
                        <p class="section-subtitle">{{ __('stages.historical_information_for_processing') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="created_at" class="form-label">
                            {{ __('stages.created_at_label') }}
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="created_at" id="created_at" class="form-input" value="{{ $stage2->created_at ?? '2025-01-15' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at" class="form-label">
                            {{ __('stages.updated_at_label') }}
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="updated_at" id="updated_at" class="form-input" value="{{ $stage2->updated_at ?? '2025-01-15' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        &nbsp;
                    </div>

                    <div class="form-group">
                        &nbsp;
                    </div>

                    <div class="form-group">
                        &nbsp;
                    </div>

                    <div class="form-group">
                        &nbsp;
                    </div>

                    <div class="form-group">
                        &nbsp;
                    </div>

                    <div class="form-group full-width">
                        &nbsp;
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ __('stages.save_changes') }}
                </button>
                <a href="{{ route('manufacturing.stage2.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    {{ __('stages.cancel_button') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('stage2Form');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.required && !this.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Smooth scroll to first error
            form.addEventListener('submit', function(e) {
                const firstInvalid = form.querySelector('.is-invalid, :invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>

@endsection
