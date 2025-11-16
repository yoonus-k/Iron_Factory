@extends('master')

@section('title', 'إضافة مجموعة عمال')

@section('content')
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            إضافة مجموعة عمال جديدة
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> لوحة التحكم</span>
            <i class="feather icon-chevron-left"></i>
            <span>مجموعات العمال</span>
            <i class="feather icon-chevron-left"></i>
            <span>إضافة مجموعة</span>
        </nav>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.worker-teams.store') }}" id="teamForm">
            @csrf

            <!-- معلومات المجموعة -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات المجموعة</h3>
                        <p class="section-subtitle">أدخل البيانات الأساسية للمجموعة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="team_code" class="form-label">
                            رقم المجموعة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            <input type="text" name="team_code" id="team_code"
                                class="form-input @error('team_code') is-invalid @enderror"
                                value="{{ old('team_code') }}" placeholder="رقم المجموعة" required readonly>
                            <button type="button" id="generateCodeBtn" class="btn-generate">
                                <span class="btn-text">توليد تلقائي</span>
                                <i class="feather icon-refresh-cw btn-icon"></i>
                            </button>
                        </div>
                        @error('team_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">
                            اسم المجموعة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" id="name"
                                class="form-input @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="مثال: مجموعة الإنتاج A" required>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">وصف المجموعة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <textarea name="description" id="description" rows="3"
                                class="form-input @error('description') is-invalid @enderror"
                                placeholder="وصف اختياري للمجموعة">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- اختيار العمال -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon account">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">اختيار العمال</h3>
                        <p class="section-subtitle">حدد العمال المنضمين لهذه المجموعة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <div class="workers-selection-header">
                            <label class="form-label">العمال النشطون</label>
                            <div class="selection-actions">
                                <button type="button" class="btn-select-all">تحديد الكل</button>
                                <button type="button" class="btn-deselect-all">إلغاء الكل</button>
                                <span class="selected-count">تم اختيار: <strong id="selectedCount">0</strong></span>
                            </div>
                        </div>

                        <div class="workers-selection" style="max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                            @forelse($workers as $worker)
                                <div class="worker-item">
                                    <input type="checkbox" 
                                           id="worker_{{ $worker->id }}" 
                                           name="workers[]" 
                                           value="{{ $worker->id }}"
                                           {{ in_array($worker->id, old('workers', [])) ? 'checked' : '' }}
                                           class="worker-checkbox">
                                    <label for="worker_{{ $worker->id }}">
                                        {{ $worker->name }} - {{ $worker->email ?? 'لا يوجد بريد' }}
                                    </label>
                                </div>
                            @empty
                                <p style="color: #999; text-align: center;">لا يوجد عمال نشطون</p>
                            @endforelse
                        </div>
                        @error('workers')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ المجموعة
                </button>
                <a href="{{ route('manufacturing.worker-teams.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <script>
        // توليد رقم المجموعة تلقائياً
        document.getElementById('generateCodeBtn').addEventListener('click', async function() {
            const button = this;
            const icon = button.querySelector('.btn-icon');
            const codeInput = document.getElementById('team_code');
            
            button.disabled = true;
            icon.classList.add('spinning');
            
            try {
                const response = await fetch('{{ route("manufacturing.worker-teams.generate-code") }}');
                const data = await response.json();
                
                if (data.team_code) {
                    codeInput.value = data.team_code;
                    button.classList.add('success');
                    setTimeout(() => button.classList.remove('success'), 2000);
                }
            } catch (error) {
                alert('حدث خطأ في توليد الرقم');
            } finally {
                button.disabled = false;
                icon.classList.remove('spinning');
            }
        });

        // تحديد/إلغاء تحديد الكل
        document.querySelector('.btn-select-all').addEventListener('click', function() {
            document.querySelectorAll('.worker-checkbox').forEach(cb => cb.checked = true);
            updateSelectedCount();
        });

        document.querySelector('.btn-deselect-all').addEventListener('click', function() {
            document.querySelectorAll('.worker-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        });

        // تحديث عداد العمال المختارين
        function updateSelectedCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // تحديث العداد عند تحميل الصفحة
        updateSelectedCount();

        // توليد رقم تلقائي عند فتح الصفحة
        window.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('team_code').value) {
                document.getElementById('generateCodeBtn').click();
            }
        });
    </script>

    <style>
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
    </style>
@endsection
