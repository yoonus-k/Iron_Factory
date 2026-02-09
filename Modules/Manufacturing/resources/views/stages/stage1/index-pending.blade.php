@extends('master')

@section('title', __('stages.stage1_first_phase') . ' - الكويلات المعلقة')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-alert-circle" style="color: #e74c3c;"></i>
                الكويلات المعلقة - المرحلة الأولى
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('stages.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('stages.first_phase') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span style="color: #e74c3c;">الكويلات المعلقة</span>
            </nav>
        </div>

        <!-- Warning Banner -->
        <div style="background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #e74c3c; box-shadow: 0 4px 15px rgba(231,76,60,0.1);">
            <h4 style="margin: 0 0 10px 0; color: #c0392b; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle"></i>
                الكويلات التالية تحتاج إلى إنهاء
            </h4>
            <p style="margin: 0; color: #922b21; font-size: 15px;">
                يجب إنهاء هذه الكويلات قبل البدء بكويل جديد. اضغط على "متابعة العمل" لإضافة استاندات أو "إنهاء الكويل" لحساب الهدر وإغلاقه.
            </p>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    <span style="color: #e74c3c;">
                        <i class="fas fa-clock"></i> الكويلات المعلقة
                    </span>
                    ({{ $pendingCoils->total() }})
                    @if(isset($canViewAllPending) && $canViewAllPending)
                        <span style="display: inline-block; background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-eye"></i> {{ __('stages.all_operations') }}
                        </span>
                    @else
                        <span style="display: inline-block; background: #10b981; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-user"></i> {{ __('stages.my_operations_only') }}
                        </span>
                    @endif
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.stage1.index') }}" class="um-btn um-btn-secondary">
                        <i class="feather icon-list"></i>
                        عرض جميع الاستاندات
                    </a>
                    <a href="{{ route('manufacturing.stage1.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        {{ __('stages.go_to_first_stage') }}
                    </a>
                </div>
            </div>

            @if($pendingCoils->count() > 0)
            <!-- Pending Coils Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; padding: 20px;">
                @foreach($pendingCoils as $coil)
                <div style="background: linear-gradient(180deg, #ffffff 0%, #fff9f9 100%); border-radius: 12px; border: 1px solid rgba(231,76,60,0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                    <!-- Card Header -->
                    <div style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; padding: 15px 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <div style="font-size: 12px; opacity: 0.9;">باركود الكويل</div>
                                <div style="font-size: 18px; font-weight: bold; font-family: monospace;">{{ $coil->parent_barcode }}</div>
                                @if($coil->transfer_status)
                                    <div style="margin-top: 8px; font-size: 13px; background: {{ $coil->transfer_status == 'pending' ? 'rgba(255,165,0,0.9)' : 'rgba(155,89,182,0.9)' }}; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                                        <i class="fas fa-share"></i> منقول إلى {{ $coil->transfer_recipient_name }} - {{ $coil->transfer_status == 'pending' ? 'بانتظار الموافقة' : 'تم القبول' }}
                                    </div>
                                @endif
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; font-weight: bold;">
                                {{ $coil->stands_count }} استاند
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d;">المادة</div>
                                <div style="font-weight: 600; color: #2c3e50;">{{ $coil->material_name }}</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d;">الوزن المنقول</div>
                                <div style="font-weight: 600; color: #2c3e50;">{{ number_format($coil->transfer_weight, 2) }} كجم</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d;">الوزن المستخدم</div>
                                <div style="font-weight: 600; color: #27ae60;">{{ number_format($coil->used_weight ?? 0, 2) }} كجم</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d;">المتبقي</div>
                                <div style="font-weight: 600; color: #e67e22;">{{ number_format($coil->transfer_weight - ($coil->used_weight ?? 0), 2) }} كجم</div>
                            </div>
                        </div>
                        
                        @if($coil->workers_names)
                        <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 15px;">
                            <i class="feather icon-users"></i> <strong>العمال:</strong> {{ $coil->workers_names }}
                            <span style="margin-right: 15px;"><i class="feather icon-clock"></i> {{ \Carbon\Carbon::parse($coil->updated_at)->diffForHumans() }}</span>
                        </div>
                        @endif
                        
                        <!-- Progress Bar -->
                        @php
                            $usedWeight = $coil->used_weight ?? 0;
                            $usagePercent = $coil->transfer_weight > 0 ? min(100, ($usedWeight / $coil->transfer_weight) * 100) : 0;
                        @endphp
                        <div style="background: #e9ecef; border-radius: 6px; height: 8px; overflow: hidden; margin-bottom: 15px;">
                            <div style="height: 100%; background: linear-gradient(90deg, #27ae60, #2ecc71); width: {{ $usagePercent }}%;"></div>
                        </div>
                        <div style="text-align: center; font-size: 13px; color: #7f8c8d;">{{ number_format($usagePercent, 1) }}% مستخدم</div>
                    </div>
                    
                    <!-- Card Actions -->
                    @php
                        $remainingWeight = $coil->transfer_weight - ($coil->used_weight ?? 0);
                        $isFullyConsumed = $remainingWeight <= 0;
                        $isTransferred = !empty($coil->transfer_status);
                        $transferDisabled = $isFullyConsumed || $isTransferred;
                    @endphp
                    <div style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #eee; display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('manufacturing.stage1.create') }}?barcode={{ $coil->parent_barcode }}" 
                           style="flex: 1; min-width: 120px; background: #3498db; color: white; padding: 12px; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-play"></i> متابعة العمل
                        </a>
                        <button type="button" onclick="finishCoil('{{ $coil->parent_barcode }}')"
                                style="flex: 1; min-width: 120px; background: #e74c3c; color: white; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-check-double"></i> إنهاء الكويل
                        </button>
                        <button type="button" 
                                onclick="{{ $transferDisabled ? 'return false;' : "showTransferModal('" . $coil->parent_barcode . "', '" . $coil->material_name . "', " . $remainingWeight . ")" }}"
                                style="flex: 1; min-width: 120px; background: {{ $transferDisabled ? '#bdc3c7' : '#9b59b6' }}; color: white; padding: 12px; border-radius: 8px; border: none; cursor: {{ $transferDisabled ? 'not-allowed' : 'pointer' }}; font-weight: 600; {{ $transferDisabled ? 'opacity: 0.6;' : '' }}"
                                {{ $transferDisabled ? 'disabled' : '' }}
                                title="{{ $isTransferred ? 'تم نقل الكويل بالفعل إلى ' . $coil->transfer_recipient_name : ($isFullyConsumed ? 'لا يمكن نقل كويل تم استهلاكه بالكامل' : 'نقل الكويل لموظف آخر') }}">
                            <i class="fas fa-share"></i> نقل لموظف
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('stages.pagination_showing') }} {{ $pendingCoils->firstItem() }} {{ __('stages.pagination_to') }} {{ $pendingCoils->lastItem() }} {{ __('stages.pagination_of') }} {{ $pendingCoils->total() }} كويل معلق
                    </p>
                </div>
                <div>
                    {{ $pendingCoils->links() }}
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div style="text-align: center; padding: 60px 20px; color: #27ae60;">
                <i class="fas fa-check-circle" style="font-size: 64px; opacity: 0.5; margin-bottom: 20px;"></i>
                <h3 style="margin: 0 0 10px 0; color: #27ae60;">لا توجد كويلات معلقة</h3>
                <p style="margin: 0 0 25px 0; color: #7f8c8d;">جميع الكويلات تم إنهاؤها بنجاح</p>
                <a href="{{ route('manufacturing.stage1.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    بدء كويل جديد
                </a>
            </div>
            @endif
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function finishCoil(barcode) {
            Swal.fire({
                title: 'إنهاء الكويل',
                text: 'هل أنت متأكد من إنهاء هذا الكويل وحساب الهدر الكلي؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، إنهاء الكويل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/stage1/finish-coil', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            material_barcode: barcode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.exceeded) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: '⚠️ تم إنهاء الكويل مع تجاوز الهدر',
                                    html: `
                                        <div style="text-align: right; direction: rtl;">
                                            <p>نسبة الهدر: <strong style="color:#e74c3c;">${data.data.waste_percentage}%</strong></p>
                                            <p>النسبة المسموحة: <strong style="color:#27ae60;">${data.data.allowed_percentage}%</strong></p>
                                            <p style="color:#856404; margin-top:15px;">تم إيقاف الاستاندات في انتظار موافقة الإدارة</p>
                                        </div>
                                    `,
                                    confirmButtonText: 'فهمت'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: '✅ تم إنهاء الكويل بنجاح',
                                    html: `
                                        <div style="text-align: right; direction: rtl;">
                                            <p>نسبة الهدر: <strong>${data.data.waste_percentage}%</strong></p>
                                            <p>عدد الاستاندات: <strong>${data.data.stands_count}</strong></p>
                                        </div>
                                    `,
                                    confirmButtonText: 'تم'
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: data.message || 'حدث خطأ أثناء إنهاء الكويل',
                                confirmButtonText: 'حسناً'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('خطأ:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء الاتصال بالخادم',
                            confirmButtonText: 'حسناً'
                        });
                    });
                }
            });
        }

        // عرض نافذة نقل الكويل لموظف آخر
        async function showTransferModal(barcode, materialName, remainingWeight = 0) {
            // التحقق من أن الكويل ليس مستهلكاً بالكامل
            if (remainingWeight <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'لا يمكن النقل',
                    text: 'لا يمكن نقل كويل تم استهلاكه بالكامل. يرجى إنهاء الكويل بدلاً من ذلك.',
                    confirmButtonText: 'فهمت'
                });
                return;
            }
            
            try {
                // جلب قائمة الموظفين
                const response = await fetch('{{ route("manufacturing.stage1.workers-for-transfer") }}');
                const data = await response.json();

                if (!data.success || !data.workers || data.workers.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'لا يوجد موظفين متاحين',
                        text: 'لا يوجد موظفين آخرين متاحين للنقل',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                // عرض نافذة اختيار الموظف
                const { value: formValues } = await Swal.fire({
                    title: 'نقل الكويل لموظف آخر',
                    width: '500px',
                    html: `
                        <div style="text-align:right; direction:rtl;">
                            <!-- معلومات الكويل -->
                            <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:15px; border-radius:10px; margin-bottom:20px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                    <div>
                                        <div style="font-size:12px; opacity:0.9;">باركود الكويل</div>
                                        <div style="font-size:16px; font-weight:bold; font-family:monospace;">${barcode}</div>
                                    </div>
                                    <div style="text-align:left;">
                                        <div style="font-size:12px; opacity:0.9;">الوزن المتبقي</div>
                                        <div style="font-size:16px; font-weight:bold;">${parseFloat(remainingWeight).toFixed(2)} كجم</div>
                                    </div>
                                </div>
                                ${materialName ? `<div style="margin-top:10px; font-size:13px; opacity:0.9;"><i class="fas fa-box"></i> ${materialName}</div>` : ''}
                            </div>

                            <!-- اختيار الموظف -->
                            <div style="margin-bottom:15px;">
                                <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                                    <i class="fas fa-user" style="color:#9b59b6;"></i> الموظف الجديد <span style="color:#e74c3c;">*</span>
                                </label>
                                <select id="swal-new-worker" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; outline:none; transition:border-color 0.3s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='#e0e0e0'">
                                    <option value="">-- اختر الموظف --</option>
                                    ${data.workers.map(w => `<option value="${w.id}">${w.name}</option>`).join('')}
                                </select>
                            </div>

                            <!-- سبب النقل -->
                            <div style="margin-bottom:15px;">
                                <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                                    <i class="fas fa-clipboard-list" style="color:#9b59b6;"></i> سبب النقل
                                </label>
                                <select id="swal-reason" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; outline:none;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='#e0e0e0'">
                                    <option value="انتهاء الوردية">انتهاء الوردية</option>
                                    <option value="انشغال الموظف">انشغال الموظف</option>
                                    <option value="توزيع العمل">توزيع العمل</option>
                                    <option value="آخر">سبب آخر</option>
                                </select>
                            </div>

                            <!-- الملاحظات -->
                            <div>
                                <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                                    <i class="fas fa-sticky-note" style="color:#9b59b6;"></i> ملاحظات (اختياري)
                                </label>
                                <textarea id="swal-notes" placeholder="أضف أي ملاحظات إضافية..." style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; min-height:70px; resize:vertical; outline:none; font-family:inherit;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='#e0e0e0'"></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-share"></i> نقل الكويل',
                    cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
                    confirmButtonColor: '#9b59b6',
                    cancelButtonColor: '#95a5a6',
                    reverseButtons: true,
                    focusConfirm: false,
                    preConfirm: () => {
                        const newWorkerId = document.getElementById('swal-new-worker').value;
                        const reason = document.getElementById('swal-reason').value;
                        const notes = document.getElementById('swal-notes').value;

                        if (!newWorkerId) {
                            Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> يجب اختيار الموظف الجديد');
                            return false;
                        }

                        return { newWorkerId, reason, notes };
                    }
                });

                if (formValues) {
                    // تنفيذ النقل
                    await executeTransfer(barcode, formValues.newWorkerId, formValues.reason, formValues.notes);
                }

            } catch (error) {
                console.error('خطأ في عرض نافذة النقل:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء تحميل بيانات النقل',
                    confirmButtonText: 'حسناً'
                });
            }
        }

        // تنفيذ نقل الكويل
        async function executeTransfer(barcode, newWorkerId, reason, notes) {
            try {
                Swal.fire({
                    title: 'جاري نقل الكويل...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch('{{ route("manufacturing.stage1.transfer-coil") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        barcode: barcode,
                        new_worker_id: newWorkerId,
                        reason: reason,
                        notes: notes
                    })
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '✅ تم نقل الكويل بنجاح',
                        html: `
                            <div style="text-align:right; direction:rtl;">
                                <p>تم نقل الكويل <strong>${barcode}</strong> إلى:</p>
                                <p style="font-size:18px; font-weight:bold; color:#27ae60;">${data.data.new_worker_name}</p>
                                <p style="color:#666; font-size:13px;">سيظهر الكويل في لوحة تحكم الموظف الجديد</p>
                            </div>
                        `,
                        confirmButtonText: 'حسناً'
                    });

                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ فشل النقل',
                        text: data.message || 'حدث خطأ أثناء نقل الكويل',
                        confirmButtonText: 'حسناً'
                    });
                }
            } catch (error) {
                console.error('خطأ في تنفيذ النقل:', error);
                Swal.fire({
                    icon: 'error',
                    title: '❌ خطأ',
                    text: 'حدث خطأ أثناء الاتصال بالخادم',
                    confirmButtonText: 'حسناً'
                });
            }
        }
    </script>

@endsection
