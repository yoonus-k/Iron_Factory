@extends('master')

@section('title', __('stands.title.show'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                {{ __('stands.header.stand_details') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('stands.breadcrumb.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <a href="{{ route('manufacturing.stands.index') }}">{{ __('stands.breadcrumb.stands') }}</a>
                <i class="feather icon-chevron-left"></i>
                <span>{{ $stand->stand_number }}</span>
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

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-info"></i>
                    {{ __('stands.card.basic_info') }}
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-primary">
                        <i class="feather icon-edit-2"></i>
                        {{ __('stands.btn.edit') }}
                    </a>
                    <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i>
                        {{ __('stands.btn.back') }}
                    </a>
                </div>
            </div>

            <!-- Stand Details -->
            <div class="um-details-section">
                <div class="um-row">
                    <!-- المعلومات الأساسية -->
                    <div class="um-col-md-6">
                        <div class="um-info-card">
                            <h5 class="um-info-card-title">
                                <i class="feather icon-file-text"></i>
                                {{ __('stands.card.basic_info') }}
                            </h5>
                            <div class="um-info-list">
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-hash"></i>
                                        رقم الاستاند:
                                    </span>
                                    <span class="um-info-value">
                                        <strong>{{ $stand->stand_number }}</strong>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-activity"></i>
                                        {{ __('stands.form.weight') }}:
                                    </span>
                                    <span class="um-info-value">
                                        <strong>{{ number_format($stand->weight, 2) }} {{ __('stands.info.weight_unit') }}</strong>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-flag"></i>
                                        المرحلة الحالية:
                                    </span>
                                    <span class="um-info-value">
                                        <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-toggle-{{ $stand->is_active ? 'right' : 'left' }}"></i>
                                        {{ __('stands.form.is_active') }}:
                                    </span>
                                    <span class="um-info-value">
                                        @if($stand->is_active)
                                            <span class="um-badge um-badge-success">{{ __('stands.active') }}</span>
                                        @else
                                            <span class="um-badge um-badge-secondary">{{ __('stands.inactive') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التواريخ والإجراءات -->
                    <div class="um-col-md-6">
                        <div class="um-info-card">
                            <h5 class="um-info-card-title">
                                <i class="feather icon-clock"></i>
                                {{ __('stands.card.dates') }}
                            </h5>
                            <div class="um-info-list">
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-calendar"></i>
                                        تاريخ الإنشاء:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->created_at->format('Y-m-d') }}
                                        <small style="color: #999;">({{ $stand->created_at->format('h:i A') }})</small>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-edit"></i>
                                        آخر تحديث:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->updated_at->format('Y-m-d') }}
                                        <small style="color: #999;">({{ $stand->updated_at->format('h:i A') }})</small>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-users"></i>
                                        منذ:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الملاحظات -->
                @if($stand->notes)
                    <div class="um-row" style="margin-top: 20px;">
                        <div class="um-col-md-12">
                            <div class="um-info-card">
                                <h5 class="um-info-card-title">
                                    <i class="feather icon-message-square"></i>
                                    {{ __('stands.form.notes') }}
                                </h5>
                                <div class="um-notes-content">
                                    {{ $stand->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- تحميل العمال والمسؤول -->
                <div class="um-row" style="margin-top: 20px;">
                    <div class="um-col-md-12">
                        <div class="um-info-card">
                            <h5 class="um-info-card-title">
                                <i class="feather icon-users"></i>
                                تحميل العمال والمسؤول
                            </h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <!-- اختيار الوردية -->
                                <div class="form-group">
                                    <label for="shift_id" style="font-weight: 600; margin-bottom: 8px; display: block; color: #374151;">
                                        <i class="feather icon-clock"></i>
                                        اختر الوردية:
                                    </label>
                                    <select id="shift_id" onchange="loadShiftData()" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                        <option value="">-- اختر الوردية --</option>
                                        @foreach($shifts ?? [] as $shift)
                                            <option value="{{ $shift->id }}" data-supervisor="{{ $shift->supervisor_id }}" data-workers="{{ json_encode($shift->worker_ids ?? []) }}">
                                                {{ $shift->shift_code }} - {{ $shift->shift_date->format('Y-m-d') }} ({{ $shift->shift_type == 'morning' ? 'الفترة الأولى' : 'الفترة الثانية' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- المسؤول الحالي -->
                                <div class="form-group">
                                    <label style="font-weight: 600; margin-bottom: 8px; display: block; color: #374151;">
                                        <i class="feather icon-user"></i>
                                        المسؤول:
                                    </label>
                                    <div id="supervisor_display" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb; color: #6b7280;">
                                        -- لم يتم اختيار وردية --
                                    </div>
                                </div>
                            </div>

                            <!-- قائمة العمال -->
                            <div style="margin-top: 15px;">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block; color: #374151;">
                                    <i class="feather icon-users"></i>
                                    العمال (<span id="worker_count">0</span>):
                                </label>
                                <div id="workers_list" style="border: 1px solid #d1d5db; border-radius: 8px; max-height: 300px; overflow-y: auto; background: #f9fafb;">
                                    <div style="padding: 20px; text-align: center; color: #9ca3af;">
                                        -- لم يتم اختيار وردية --
                                    </div>
                                </div>
                            </div>

                            <!-- زر نقل العمال -->
                            <div style="margin-top: 15px; display: flex; gap: 10px;">
                                <button onclick="transferToShift()" class="um-btn um-btn-primary" style="flex: 1;">
                                    <i class="feather icon-arrow-down"></i>
                                    نقل هذه العمال للوردية
                                </button>
                                <button onclick="loadShiftData()" class="um-btn um-btn-outline" style="flex: 1;">
                                    <i class="feather icon-refresh-cw"></i>
                                    تحديث
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="um-form-actions">
                <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-primary">
                    <i class="feather icon-edit-2"></i>
                    {{ __('stands.btn.edit') }}
                </a>

                <form action="{{ route('manufacturing.stands.toggle-status', $stand->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="um-btn um-btn-{{ $stand->is_active ? 'warning' : 'success' }}">
                        <i class="feather icon-{{ $stand->is_active ? 'pause' : 'play' }}-circle"></i>
                        {{ $stand->is_active ? __('stands.btn.disable') : __('stands.btn.enable') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('manufacturing.stands.destroy', $stand->id) }}" style="display: inline;" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="um-btn um-btn-danger">
                        <i class="feather icon-trash-2"></i>
                        {{ __('stands.btn.delete') }}
                    </button>
                </form>
            </div>
        </section>
    </div>

    <style>
        .um-details-section {
            padding: 20px;
        }

        .um-info-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
        }

        .um-info-card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .um-info-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .um-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .um-info-item:hover {
            background: #f0f0f0;
        }

        .um-info-label {
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .um-info-value {
            font-weight: 500;
            color: #333;
            text-align: left;
        }

        .um-notes-content {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            line-height: 1.6;
            color: #555;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {
            .um-row {
                flex-direction: column;
            }

            .um-col-md-6 {
                width: 100%;
                margin-bottom: 15px;
            }

            .um-form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .um-form-actions .um-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('{{ __('stands.alert.confirm_delete') }}\n\n{{ __('stands.alert.confirm_delete_warning') }}')) {
                        this.submit();
                    }
                });
            }

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

            // تحميل العمال الحاليين في الاستاند عند تحميل الصفحة
            loadCurrentStandWorkers();
        });

        // تحميل العمال الحاليين في الاستاند من الوردية (ShiftAssignment.worker_ids) - ليس من WorkerStageHistory
        function loadCurrentStandWorkers() {
            const standId = {{ $stand->id }};

            fetch(`/manufacturing/stands/${standId}/current-workers`)
                .then(response => response.json())
                .then(data => {
                    console.log('Stand workers data from Shift:', data);
                    const workers = data.workers || [];
                    let workersHtml = '';

                    // عرض معلومات الوردية والمسؤول
                    if (data.supervisor) {
                        let supervisorHtml = `
                            <div style="padding: 15px; background: #f0f4ff; border-radius: 8px; margin-bottom: 15px; border-right: 4px solid #0066b2;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066b2" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280;">المسؤول</div>
                                        <div style="font-weight: 600; color: #1f2937; font-size: 15px;">${data.supervisor.name}</div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // إذا كانت هناك معلومات وردية، أضفها
                        if (data.shift_code) {
                            supervisorHtml += `
                                <div style="padding: 15px; background: #f0f8f0; border-radius: 8px; margin-bottom: 15px; border-right: 4px solid #10b981;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <path d="M12 1v6"></path>
                                            <path d="M4.22 4.22l4.24 4.24"></path>
                                            <path d="M1 12h6"></path>
                                            <path d="M4.22 19.78l4.24-4.24"></path>
                                            <path d="M12 23v-6"></path>
                                            <path d="M19.78 19.78l-4.24-4.24"></path>
                                            <path d="M23 12h-6"></path>
                                            <path d="M19.78 4.22l-4.24 4.24"></path>
                                        </svg>
                                        <div>
                                            <div style="font-size: 12px; color: #6b7280;">الوردية</div>
                                            <div style="font-weight: 600; color: #1f2937; font-size: 15px;">${data.shift_code}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }

                        document.getElementById('supervisor_display').innerHTML = supervisorHtml;
                    }

                    if (workers.length > 0) {
                        let workersTitle = `
                            <div style="padding: 12px; background: #fef3c7; border-radius: 6px; margin-bottom: 10px; text-align: center; font-weight: 600; color: #92400e;">
                                👷 عدد العمال: ${workers.length}
                            </div>
                        `;
                        workersHtml = workersTitle;

                        workers.forEach(worker => {
                            workersHtml += `
                                <div style="padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                                    <div style="flex: 1;">
                                        <div style="color: #1f2937; font-weight: 600;">${worker.name}</div>
                                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                            <strong>كود:</strong> <span style="color: #0066b2;">${worker.worker_code}</span> |
                                            <strong>الوظيفة:</strong> ${worker.position || 'غير محددة'}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        console.log('Loaded ' + workers.length + ' workers from shift');
                    } else {
                        workersHtml = '<div style="padding: 20px; text-align: center; color: #9ca3af;">لا توجد عمال حاليين في هذا الاستاند</div>';
                    }

                    // تحديث عدد العمال والقائمة
                    document.getElementById('workers_list').innerHTML = workersHtml;
                    document.getElementById('worker_count').textContent = workers.length;

                    // حفظ shift_id الحالي للاستخدام في النقل
                    if (data.shift_id) {
                        document.getElementById('shift_id').dataset.currentShiftId = data.shift_id;
                    }
                })
                .catch(error => {
                    console.error('Error loading stand workers:', error);
                    document.getElementById('workers_list').innerHTML = '<div style="padding: 20px; text-align: center; color: #ef4444;">❌ خطأ في تحميل العمال: ' + error.message + '</div>';
                    document.getElementById('supervisor_display').innerHTML = '<div style="padding: 20px; text-align: center; color: #ef4444;">❌ خطأ في تحميل بيانات المسؤول</div>';
                });
        }

        // تحميل بيانات الوردية (اختياري - للاختيار من dropdown)
        function loadShiftData() {
            const shiftSelect = document.getElementById('shift_id');
            const selectedOption = shiftSelect.options[shiftSelect.selectedIndex];

            if (!selectedOption.value) {
                document.getElementById('supervisor_display').innerHTML = '-- لم يتم اختيار وردية --';
                document.getElementById('workers_list').innerHTML = '<div style="padding: 20px; text-align: center; color: #9ca3af;">-- لم يتم اختيار وردية --</div>';
                document.getElementById('worker_count').textContent = '0';
                return;
            }

            // الحصول على بيانات المسؤول والعمال من data attributes
            const supervisorId = selectedOption.getAttribute('data-supervisor');
            const workersJson = selectedOption.getAttribute('data-workers');

            try {
                const workerIds = JSON.parse(workersJson) || [];

                // عرض المسؤول
                if (supervisorId) {
                    fetch(`/manufacturing/shifts-workers/get-supervisor/${supervisorId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('supervisor_display').innerHTML =
                                `<strong style="color: #1f2937;">${data.name || 'غير محدد'}</strong>`;
                        })
                        .catch(error => {
                            document.getElementById('supervisor_display').innerHTML = '<strong>خطأ في تحميل المسؤول</strong>';
                        });
                } else {
                    document.getElementById('supervisor_display').innerHTML = '<strong style="color: #9ca3af;">لم يتم تحديد مسؤول</strong>';
                }

                // عرض العمال
                if (workerIds.length > 0) {
                    fetch('/manufacturing/shifts-workers/get-workers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ worker_ids: workerIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Shift workers data received:', data);
                        const workers = data.workers || [];
                        let workersHtml = '';

                        workers.forEach(worker => {
                            workersHtml += `
                                <div style="padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; hover: background #f3f4f6;">
                                    <div style="flex: 1;">
                                        <div style="color: #1f2937; font-weight: 500;">${worker.name}</div>
                                        <div style="font-size: 12px; color: #6b7280;">
                                            <span>كود: ${worker.worker_code}</span>
                                            ${worker.position ? ` | الموضع: ${worker.position}` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        document.getElementById('workers_list').innerHTML = workersHtml || '<div style="padding: 20px; text-align: center; color: #9ca3af;">لا توجد عمال</div>';
                        document.getElementById('worker_count').textContent = workers.length;
                    })
                    .catch(error => {
                        console.error('Error loading workers:', error);
                        document.getElementById('workers_list').innerHTML = '<div style="padding: 20px; text-align: center; color: #ef4444;">خطأ في تحميل العمال</div>';
                    });
                } else {
                    document.getElementById('workers_list').innerHTML = '<div style="padding: 20px; text-align: center; color: #9ca3af;">لا توجد عمال في هذه الوردية</div>';
                    document.getElementById('worker_count').textContent = '0';
                }
            } catch (error) {
                console.error('Error parsing workers:', error);
                document.getElementById('workers_list').innerHTML = '<div style="padding: 20px; text-align: center; color: #ef4444;">خطأ في معالجة البيانات</div>';
            }
        }

        // نقل العمال للوردية
        function transferToShift() {
            const shiftSelect = document.getElementById('shift_id');

            if (!shiftSelect.value) {
                alert('يرجى اختيار الوردية أولاً');
                return;
            }

            const workersList = document.getElementById('workers_list');
            const workerElements = workersList.querySelectorAll('div[style*="border-bottom"]');

            if (workerElements.length === 0) {
                alert('لا توجد عمال لنقلهم');
                return;
            }

            const standId = {{ $stand->id }};
            const toShiftId = parseInt(shiftSelect.value);

            // الحصول على معرف الوردية الحالية
            const currentShiftCode = shiftSelect.options[shiftSelect.selectedIndex].textContent;

            const confirmTransfer = confirm(`هل تريد نقل ${workerElements.length} عامل للوردية:\n${currentShiftCode}\n\nسيتم التسجيل في جدول الموافقات (قبل وبعد)`);
            if (!confirmTransfer) {
                return;
            }

            // استخراج بيانات العمال من العرض
            let workerIds = [];
            workerElements.forEach(el => {
                const text = el.textContent;
                const codeMatch = text.match(/كود:\s*(\d+)/);
                if (codeMatch) {
                    // سنستخرج من خلال API البيانات الفعلية
                    workerIds.push(parseInt(codeMatch[1]));
                }
            });

            // إذا لم نتمكن من استخراج الأكواد، نجلب البيانات من API مرة أخرى
            if (workerIds.length === 0) {
                fetch(`/manufacturing/stands/${standId}/current-workers`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.workers && data.workers.length > 0) {
                            workerIds = data.workers.map(w => w.id);
                            performTransfer(standId, data.shift_id, toShiftId, workerIds);
                        }
                    });
            } else {
                performTransfer(standId, null, toShiftId, workerIds);
            }
        }

        function performTransfer(standId, fromShiftId, toShiftId, workerIds) {
            // إذا لم نحصل على fromShiftId، نجلبه من API
            if (!fromShiftId) {
                fetch(`/manufacturing/stands/${standId}/current-workers`)
                    .then(response => response.json())
                    .then(data => {
                        fromShiftId = data.shift_id;
                        sendTransferRequest(standId, fromShiftId, toShiftId, workerIds);
                    });
            } else {
                sendTransferRequest(standId, fromShiftId, toShiftId, workerIds);
            }
        }

        function sendTransferRequest(standId, fromShiftId, toShiftId, workerIds) {
            fetch(`/manufacturing/stands/${standId}/transfer-workers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    from_shift_id: fromShiftId,
                    to_shift_id: toShiftId,
                    worker_ids: workerIds,
                    transfer_notes: 'نقل مباشر من صفحة الاستاند'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const beforeWorkers = data.before.workers.map(w => '• ' + w.name).join('\n');
                    const afterWorkers = data.after.workers.map(w => '• ' + w.name).join('\n');

                    alert('✅ تم نقل العمال بنجاح!\n\n📋 من: ' + data.before.from_shift_code +
                          '\n📋 إلى: ' + data.after.to_shift_code +
                          '\n\n📌 العمال:\n' + afterWorkers +
                          '\n\n✔️ تم التسجيل في الموافقات برقم: #' + data.handover_id);

                    setTimeout(() => location.reload(), 2000);
                } else {
                    alert('❌ خطأ: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Transfer Error:', error);
                alert('❌ حدث خطأ في النقل: ' + error.message);
            });
        }
    </script>

@endsection
