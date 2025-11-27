@extends('master')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/role.css') }}">
<div class="form-container">
    <!-- Header -->
    <div class="um-header-section">
        <div class="header-content">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="7"></circle>
                    <path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.11"></path>
                </svg>
                تعديل الدور: {{ $role->role_name }}
            </h1>
            <p class="header-subtitle">عدّل تفاصيل الدور والصلاحيات المرتبطة به</p>
        </div>
        <nav class="um-breadcrumb-nav">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> لوحة التحكم
            </a>
            <svg class="breadcrumb-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <a href="{{ route('roles.index') }}">الأدوار</a>
            <svg class="breadcrumb-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>تعديل الدور</span>
        </nav>
    </div>
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
        <div class="um-alert-custom um-alert-error" role="alert">
            <i class="feather icon-x-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('roles.update', $role) }}" id="roleForm">
            @csrf
            @method('PUT')

            <!-- Role Info Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="7"></circle>
                            <path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.11"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">بيانات الدور</h3>
                        <p class="section-subtitle">عدّل تفاصيل الدور واختر صلاحياته</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="display_name" class="form-label">
                            اسم الدور
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20l9-5-9-5-9 5 9 5z"></path>
                                <path d="M12 4l9 5-9 5-9-5 9-5z"></path>
                            </svg>
                            <input id="display_name" type="text" name="display_name" class="form-input @error('display_name') is-invalid @enderror" value="{{ old('display_name', $role->role_name) }}" required>
                        </div>
                        @error('display_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">
                            الاسم الوصفي (الكود)
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16M4 12h16M4 17h16"></path>
                            </svg>
                            <input id="name" type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $role->role_code) }}">
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="level" class="form-label">
                            المستوى (0-100)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M2 12h20"></path>
                            </svg>
                            <input id="level" type="number" name="level" class="form-input @error('level') is-invalid @enderror" min="0" max="100" value="{{ old('level', $role->level) }}" required>
                        </div>
                        @error('level')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">الوصف</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z"></path>
                                <path d="M8 8h8M8 12h8M8 16h6"></path>
                            </svg>
                            <textarea id="description" name="description" rows="3" class="form-input @error('description') is-invalid @enderror">{{ old('description', $role->description) }}</textarea>
                        </div>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_active" class="form-label">الدور نشط</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <select id="is_active" name="is_active" class="form-input @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $role->is_active) ? 'selected' : '' }}>نعم</option>
                                <option value="0" {{ !old('is_active', $role->is_active) ? 'selected' : '' }}>لا</option>
                            </select>
                        </div>
                        @error('is_active')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <path d="M7 9h10M7 13h7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">الصلاحيات</h3>
                        <p class="section-subtitle">يمكنك تحديد كل صلاحيات قسم معيّن بضغطة واحدة</p>
                    </div>
                </div>
            </div>

            @php
                use Illuminate\Support\Str;
                $grouped = $permissions->groupBy('group_name');
                $selected = old('permission_ids', $assigned ?? []);
            @endphp

            <!-- Permission Groups Cards -->
            @foreach ($grouped as $group => $items)
                @php $groupKey = Str::slug($group ?: 'غير-مصنّف'); @endphp
                <div class="form-card permissions-group" data-group="{{ $groupKey }}">
                    <div class="section-subgroup-header">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <strong class="group-title">{{ $group ?: 'غير مصنّف' }}</strong>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input check-all" id="check_all_{{ $groupKey }}" data-group="{{ $groupKey }}">
                                <label class="form-check-label" for="check_all_{{ $groupKey }}">تحديد الكل</label>
                            </div>
                        </div>
                    </div>

                    <div class="permissions-grid">
                        @foreach ($items as $perm)
                            <div class="form-check permission-item">
                                <input class="form-check-input perm-checkbox"
                                       type="checkbox"
                                       name="permission_ids[]"
                                       value="{{ $perm->id }}"
                                       id="perm_{{ $perm->id }}"
                                       data-group="{{ $groupKey }}"
                                       {{ in_array($perm->id, $selected) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $perm->id }}">
                                    {{ $perm->display_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @error('permission_ids')
                <div class="form-card">
                    <span class="error-message">{{ $message }}</span>
                </div>
            @enderror

            <!-- Form Actions Card -->
            <div class="form-card">
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        حفظ
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // جمع أسماء المجموعات الحقيقية من الـ HTML
        const groupsMap = {};
        document.querySelectorAll('.check-all').forEach(function(checkbox) {
            const groupKey = checkbox.getAttribute('data-group');
            groupsMap[groupKey] = groupKey;
        });

        console.log('مجموعات الصلاحيات:', groupsMap);

        // البنية الهرمية: كل مجموعة فرعية تشير إلى آبائها
        const hierarchy = {
            // المستودع الرئيسي
            'almstw-almtwh-amkhm': ['almstw'],
            'almstw-almtwh-alstlm': ['almstw'],
            'almstw-fwtar-alshra': ['almstw'],
            'almstw-almwrudn': ['almstw'],
            'almstw-anwa-almwd': ['almstw'],
            'almstw-whdat-alqyas': ['almstw'],
            'almstw-tsjil-albda': ['almstw'],
            'almstw-tswya-almstw': ['almstw'],
            'almstw-hrkdt-almwd': ['almstw'],
            'almstw-alaeadat': ['almstw'],
            'almstw-tqarir': ['almstw']
        };

        function updateGroupState(groupKey) {
            const groupItems = document.querySelectorAll('.perm-checkbox[data-group="' + groupKey + '"]');
            const checkAll = document.getElementById('check_all_' + groupKey);
            if (!checkAll) return;

            const checkedCount = Array.from(groupItems).filter(cb => cb.checked).length;

            // إذا كان هناك أي عنصر محدد، حدد "تحديد الكل"
            if (checkedCount > 0) {
                checkAll.checked = true;
                checkAll.indeterminate = false;
            } else {
                // إذا لم يكن هناك أي عنصر محدد، ألغ "تحديد الكل"
                checkAll.checked = false;
                checkAll.indeterminate = false;
            }

            // تحديث الآباء تلقائياً
            if (hierarchy[groupKey]) {
                hierarchy[groupKey].forEach(function(parentKey) {
                    updateGroupState(parentKey);
                });
            }
        }

        // تفعيل زر "تحديد الكل" عند الضغط عليه
        document.querySelectorAll('.check-all').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const groupKey = this.getAttribute('data-group');
                const isChecked = this.checked;

                document.querySelectorAll('.perm-checkbox[data-group="' + groupKey + '"]').forEach(function(cb) {
                    cb.checked = isChecked;
                });

                updateGroupState(groupKey);

                // إذا كان الآب مفعل، فعل جميع الأطفال
                if (isChecked) {
                    Object.keys(hierarchy).forEach(function(childKey) {
                        if (hierarchy[childKey] && hierarchy[childKey].includes(groupKey)) {
                            const childCheckAll = document.getElementById('check_all_' + childKey);
                            if (childCheckAll) {
                                childCheckAll.checked = true;
                                childCheckAll.indeterminate = false;
                            }
                            document.querySelectorAll('.perm-checkbox[data-group="' + childKey + '"]').forEach(function(cb) {
                                cb.checked = true;
                            });
                            updateGroupState(childKey);
                        }
                    });
                }
            });

            // تحديث الحالة الأولية لكل مجموعة
            updateGroupState(toggle.getAttribute('data-group'));
        });

        // تحديث "تحديد الكل" تلقائياً عند تحديد/إلغاء تحديد أي صلاحية
        document.querySelectorAll('.perm-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const groupKey = this.getAttribute('data-group');
                updateGroupState(groupKey);
            });
        });
    });
</script>
@endsection
