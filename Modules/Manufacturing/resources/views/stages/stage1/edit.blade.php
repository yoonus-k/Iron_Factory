@extends('master')

@section('title', __('stages.edit_stand_data'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            {{ __('stages.edit_stand_data') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> {{ __('stages.dashboard') }}
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.first_phase') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.edit_stand') }}</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage1.update', $stage1->id ?? 1) }}" id="stage1Form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Material Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('stages.material_information') }}</h3>
                        <p class="section-subtitle">{{ __('stages.update_basic_data') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="barcode" class="form-label">
                            {{ __('stages.barcode') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <input type="text" name="barcode" id="barcode" class="form-input" value="ST1-001-2025" placeholder="{{ __('stages.placeholder_barcode') }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="material_id" class="form-label">
                            {{ __('stages.material_type') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <select name="material_id" id="material_id" class="form-input" required>
                                <option value="">{{ __('stages.choose_raw_material') }}</option>
                                <option value="1" selected>{{ __('stages.raw_material_1') }}</option>
                                <option value="2">{{ __('stages.raw_material_2') }}</option>
                                <option value="3">{{ __('stages.raw_material_3') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stand_number" class="form-label">
                            {{ __('stages.stand_number') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <input type="text" name="stand_number" id="stand_number" class="form-input" value="ST-001" placeholder="{{ __('stages.placeholder_stand_number') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="wire_size" class="form-label">
                            {{ __('stages.wire_size') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <input type="number" name="wire_size" id="wire_size" class="form-input" value="2.5" placeholder="{{ __('stages.placeholder_wire_size') }}" step="0.1" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weight" class="form-label">{{ __('stages.weight') }} <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="weight" id="weight" class="form-input" value="250" placeholder="{{ __('stages.placeholder_weight') }}" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="waste_percentage" class="form-label">{{ __('stages.waste_percentage_field') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <input type="number" name="waste_percentage" id="waste_percentage" class="form-input" value="5.2" placeholder="{{ __('stages.placeholder_waste_percentage') }}" step="0.01" min="0" max="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">{{ __('stages.status') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                            <select name="status" id="status" class="form-input">
                                <option value="created">{{ __('stages.stand_status_created') }}</option>
                                <option value="in_process">{{ __('stages.stand_status_in_process') }}</option>
                                <option value="completed" selected>{{ __('stages.stand_status_completed') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes" class="form-label">{{ __('stages.notes') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="{{ __('stages.placeholder_notes') }}">{{ __('stages.notes_example') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        &nbsp;
                    </div>

                    <div class="form-group full-width">
                        <div class="switch-group">
                            <input type="checkbox" id="is_active" name="is_active" class="switch-input" checked>
                            <label for="is_active" class="switch-label">
                                <span class="switch-button"></span>
                                <span class="switch-text">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    {{ __('stages.activate_stand') }}
                                </span>
                            </label>
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
                        <p class="section-subtitle">{{ __('stages.update_additional_info') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="created_at" class="form-label">
                            {{ __('stages.created_at') }}
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="created_at" id="created_at" class="form-input" value="2025-01-15" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at" class="form-label">
                            {{ __('stages.updated_at') }}
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="updated_at" id="updated_at" class="form-input" value="2025-01-15" readonly>
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
                    {{ __('stages.save_changes_button') }}
                </button>
                <a href="{{ route('manufacturing.stage1.index') }}" class="btn-cancel">
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
            const form = document.getElementById('stage1Form');
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
