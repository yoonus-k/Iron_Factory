@extends('master')

@section('title', __('stages.stage2_processing') . ' - الاستاندات المعلقة')

@section('content')

@php
    $canViewAllStage2 = auth()->user()?->hasPermission('VIEW_ALL_STAGE2_OPERATIONS');
@endphp

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title" style="color:#8e44ad;">
            <i class="feather icon-alert-circle" style="color:#8e44ad;"></i>
            الاستاندات المعلقة - المرحلة الثانية
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> {{ __('stages.dashboard') }}
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.stage2_processing') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span style="color:#8e44ad;">الاستاندات المعلقة</span>
        </nav>
    </div>

    <!-- Warning Banner -->
    <div style="background: linear-gradient(135deg, #faf0ff 0%, #f4e3ff 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #8e44ad; box-shadow: 0 4px 15px rgba(142,68,173,0.12);">
        <h4 style="margin: 0 0 10px 0; color: #661a82; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-triangle"></i>
            الاستاندات التالية تحتاج إلى استكمال أو إنهاء
        </h4>
        <p style="margin: 0; color: #5c2b64; font-size: 15px;">
            يجب إنهاء هذه الاستاندات قبل البدء باستاند جديد. استخدم "استكمال العمل" للعودة للمرحلة الثانية أو "نقل الاستاند" لإسناده لموظف آخر.
        </p>
    </div>

    <!-- Main Card -->
    <section class="um-main-card">
        <div class="um-card-header">
            <h4 class="um-card-title">
                <i class="feather icon-list"></i>
                <span style="color: #8e44ad;">
                    <i class="fas fa-clock"></i> الاستاندات المعلقة
                </span>
                (<span id="pendingStandsCount">0</span>)
                @if($canViewAllStage2)
                    <span style="display: inline-block; background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                        <i class="feather icon-eye"></i> {{ __('stages.all_operations') }}
                    </span>
                @else
                    <span style="display: inline-block; background: #10b981; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                        <i class="feather icon-user"></i> {{ __('stages.my_operations_only') }}
                    </span>
                @endif
            </h4>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="um-btn um-btn-secondary" onclick="loadPendingStands()">
                    <i class="feather icon-refresh-cw"></i>
                    تحديث
                </button>
                <a href="{{ route('manufacturing.stage2.index') }}" class="um-btn um-btn-secondary">
                    <i class="feather icon-list"></i>
                    عرض جميع الاستاندات
                </a>
                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="background:#8e44ad; border-color:#8e44ad;">
                    <i class="feather icon-plus"></i>
                    الانتقال للمعالجة
                </a>
            </div>
        </div>

        <div id="pendingCardsWrapper">
            <div id="pendingCardsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; padding: 20px;">
                <div style="text-align:center; padding:60px 20px; color:#8e44ad;">
                    <i class="fas fa-spinner fa-spin" style="font-size:48px; margin-bottom:15px;"></i>
                    <h3 style="margin:0;">جاري تحميل البيانات...</h3>
                </div>
            </div>

            <div id="pendingEmptyState" style="display:none; text-align:center; padding:60px 20px; color:#27ae60;">
                <i class="fas fa-check-circle" style="font-size:64px; opacity:0.4; margin-bottom:20px;"></i>
                <h3 style="margin:0 0 10px 0;">لا توجد استاندات معلقة</h3>
                <p style="margin:0 0 25px 0; color:#7f8c8d;">جميع الاستاندات تم إنهاؤها بنجاح</p>
                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="background:#8e44ad; border-color:#8e44ad;">
                    <i class="feather icon-plus"></i>
                    بدء استانداً جديداً
                </a>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let pendingStands = [];

document.addEventListener('DOMContentLoaded', loadPendingStands);

async function loadPendingStands() {
    const grid = document.getElementById('pendingCardsGrid');
    const emptyState = document.getElementById('pendingEmptyState');
    emptyState.style.display = 'none';
    grid.innerHTML = `
        <div style="text-align:center; padding:60px 20px; color:#8e44ad;">
            <i class="fas fa-spinner fa-spin" style="font-size:48px; margin-bottom:15px;"></i>
            <h3 style="margin:0;">جاري تحديث البيانات...</h3>
        </div>
    `;

    try {
        const response = await fetch('/stage2/pending-items', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            pendingStands = data.items || [];
            renderPendingStands();
        } else {
            showPendingError(data.message || 'فشل تحميل البيانات');
        }
    } catch (error) {
        console.error('خطأ أثناء تحميل الاستاندات:', error);
        showPendingError('حدث خطأ أثناء الاتصال بالخادم');
    }
}

function renderPendingStands() {
    const grid = document.getElementById('pendingCardsGrid');
    const emptyState = document.getElementById('pendingEmptyState');
    const countSpan = document.getElementById('pendingStandsCount');

    countSpan.textContent = pendingStands.length;

    if (!pendingStands.length) {
        grid.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    grid.innerHTML = pendingStands.map(stand => buildStandCard(stand)).join('');
}

function buildStandCard(stand) {
    const remaining = parseFloat(stand.remaining_weight || 0);
    const processed = parseFloat(stand.total_processed || 0);
    const totalWeight = remaining + processed;
    const usagePercent = totalWeight > 0 ? Math.min(100, (processed / totalWeight) * 100) : 0;
    const materialName = escapeQuotes(stand.material_name || 'غير محدد');
    const isConsumed = remaining <= 0;

    return `
        <div style="background: linear-gradient(180deg, #ffffff 0%, #fef7ff 100%); border-radius: 12px; border: 1px solid rgba(142,68,173,0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background: linear-gradient(135deg, #8e44ad 0%, #6c1b9d 100%); color: white; padding: 15px 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 12px; opacity: 0.9;">باركود الاستاند</div>
                        <div style="font-size: 18px; font-weight: bold; font-family: monospace;">${stand.barcode}</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; font-weight: bold;">
                        ${stand.pending_count || 0} معالجة
                    </div>
                </div>
            </div>
            
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <div style="font-size: 12px; color: #7f8c8d;">المادة</div>
                        <div style="font-weight: 600; color: #2c3e50;">${stand.material_name || 'غير محدد'}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #7f8c8d;">مقاس السلك</div>
                        <div style="font-weight: 600; color: #2c3e50;">${stand.wire_size || 0} مم</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #7f8c8d;">إجمالي المعالج</div>
                        <div style="font-weight: 600; color: #27ae60;">${processed.toFixed(2)} كجم</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #7f8c8d;">المتبقي</div>
                        <div style="font-weight: 600; color: #e67e22;">${remaining.toFixed(2)} كجم</div>
                    </div>
                </div>

                <div style="background: #e9ecef; border-radius: 6px; height: 8px; overflow: hidden; margin-bottom: 12px;">
                    <div style="height: 100%; background: linear-gradient(90deg, #27ae60, #2ecc71); width: ${usagePercent}%;"></div>
                </div>
                <div style="text-align: center; font-size: 13px; color: #7f8c8d;">${usagePercent.toFixed(1)}% من الوزن مستخدم</div>
            </div>
            
            <div style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #eee; display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('manufacturing.stage2.create') }}?barcode=${stand.barcode}" 
                   style="flex: 1; min-width: 120px; background: #3498db; color: white; padding: 12px; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-play"></i> استكمال العمل
                </a>
                <button type="button" onclick="navigateToFinish('${stand.barcode}')"
                        style="flex: 1; min-width: 120px; background: #27ae60; color: white; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-check-double"></i> إنهاء الاستاند
                </button>
                <button type="button" 
                        onclick="showTransferModal('${stand.barcode}', '${materialName}', ${remaining.toFixed(2)})"
                        style="flex: 1; min-width: 120px; background: ${isConsumed ? '#bdc3c7' : '#9b59b6'}; color: white; padding: 12px; border-radius: 8px; border: none; cursor: ${isConsumed ? 'not-allowed' : 'pointer'}; font-weight: 600; ${isConsumed ? 'opacity:0.6;' : ''}"
                        ${isConsumed ? 'disabled' : ''}>
                    <i class="fas fa-share"></i> نقل الاستاند
                </button>
            </div>
        </div>
    `;
}

function showPendingError(message) {
    const grid = document.getElementById('pendingCardsGrid');
    const emptyState = document.getElementById('pendingEmptyState');
    emptyState.style.display = 'none';
    grid.innerHTML = `
        <div style="text-align:center; padding:60px 20px; color:#e74c3c;">
            <i class="fas fa-exclamation-circle" style="font-size:48px; margin-bottom:15px;"></i>
            <h3 style="margin:0 0 10px 0;">حدث خطأ</h3>
            <p style="margin:0 0 20px 0; color:#7f8c8d;">${message}</p>
            <button type="button" class="um-btn um-btn-secondary" onclick="loadPendingStands()">
                <i class="feather icon-refresh-cw"></i>
                إعادة المحاولة
            </button>
        </div>
    `;
}

function escapeQuotes(value) {
    return String(value || '').replace(/'/g, "\\'");
}

function navigateToFinish(barcode) {
    Swal.fire({
        title: 'إنهاء الاستاند',
        text: 'سيتم فتح شاشة المرحلة الثانية لإنهاء الاستاند وحساب الهدر. هل ترغب بالمتابعة؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، متابعة',
        cancelButtonText: 'إلغاء'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = `{{ route('manufacturing.stage2.create') }}?barcode=${barcode}&focus=finish`;
        }
    });
}

async function showTransferModal(barcode, materialName, remainingWeight = 0) {
    if (remainingWeight <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'لا يمكن النقل',
            text: 'لا يمكن نقل استاند تم استهلاكه بالكامل. يرجى إنهاؤه بدلاً من ذلك.',
            confirmButtonText: 'فهمت'
        });
        return;
    }

    try {
        const response = await fetch('/stage2/workers-for-transfer', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();

        if (!data.success || !data.workers || !data.workers.length) {
            Swal.fire({
                icon: 'warning',
                title: 'لا يوجد موظفين متاحين',
                text: 'لا يوجد موظفين آخرين متاحين للنقل حاليًا',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        const { value: formValues } = await Swal.fire({
            title: 'نقل الاستاند لموظف آخر',
            width: '500px',
            html: `
                <div style="text-align:right; direction:rtl;">
                    <div style="background:linear-gradient(135deg,#8e44ad 0%,#6c1b9d 100%); color:white; padding:15px; border-radius:10px; margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                <div style="font-size:12px; opacity:0.9;">باركود الاستاند</div>
                                <div style="font-size:16px; font-weight:bold; font-family:monospace;">${barcode}</div>
                            </div>
                            <div style="text-align:left;">
                                <div style="font-size:12px; opacity:0.9;">الوزن المتبقي</div>
                                <div style="font-size:16px; font-weight:bold;">${remainingWeight.toFixed(2)} كجم</div>
                            </div>
                        </div>
                        <div style="margin-top:10px; font-size:13px; opacity:0.9;"><i class="fas fa-box"></i> ${materialName || 'غير محدد'}</div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-user" style="color:#8e44ad;"></i> الموظف الجديد <span style="color:#e74c3c;">*</span>
                        </label>
                        <select id="swal-new-worker" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="">-- اختر الموظف --</option>
                            ${data.workers.map(w => `<option value="${w.id}">${w.name}</option>`).join('')}
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-clipboard-list" style="color:#8e44ad;"></i> سبب النقل
                        </label>
                        <select id="swal-reason" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="انتهاء الوردية">انتهاء الوردية</option>
                            <option value="انشغال الموظف">انشغال الموظف</option>
                            <option value="توزيع العمل">توزيع العمل</option>
                            <option value="آخر">سبب آخر</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-sticky-note" style="color:#8e44ad;"></i> ملاحظات (اختياري)
                        </label>
                        <textarea id="swal-notes" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; min-height:70px;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-share"></i> نقل الاستاند',
            cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
            confirmButtonColor: '#9b59b6',
            cancelButtonColor: '#95a5a6',
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
            await executeStandTransfer(barcode, formValues.newWorkerId, formValues.reason, formValues.notes);
        }

    } catch (error) {
        console.error('خطأ في تحميل بيانات النقل:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء تحميل بيانات النقل',
            confirmButtonText: 'حسناً'
        });
    }
}

async function executeStandTransfer(barcode, newWorkerId, reason, notes) {
    try {
        Swal.fire({
            title: 'جاري نقل الاستاند...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const response = await fetch('/stage2/transfer-stand', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode,
                new_worker_id: newWorkerId,
                reason,
                notes
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: '✅ تم نقل الاستاند بنجاح',
                html: `<div style="text-align:right; direction:rtl;">
                        <p>تم نقل الاستاند <strong>${barcode}</strong> إلى:</p>
                        <p style="font-size:18px; font-weight:bold; color:#27ae60;">${data.data.new_worker_name}</p>
                    </div>`,
                confirmButtonText: 'حسناً'
            });
            loadPendingStands();
        } else {
            throw new Error(data.message || 'فشل نقل الاستاند');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ فشل النقل',
            text: error.message,
            confirmButtonText: 'حسناً'
        });
    }
}
</script>

@endsection
