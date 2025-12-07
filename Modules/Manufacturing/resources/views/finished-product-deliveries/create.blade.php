@extends('master')

@section('title', 'إنشاء إذن صرف منتجات نهائية')

@section('content')
<style>
    .box-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
        position: relative;
    }
    
    .box-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 50px 50px 0;
        border-color: transparent #27ae60 transparent transparent;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .box-card.selected::before {
        opacity: 1;
    }
    
    .box-card.selected::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        color: white;
        font-weight: bold;
        font-size: 18px;
        z-index: 1;
    }
    
    .box-card:hover {
        border-color: #667eea;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
        transform: translateY(-3px);
    }
    
    .box-card.selected {
        border-color: #27ae60;
        background: linear-gradient(135deg, #f0fff4 0%, #e6f9ea 100%);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.3);
    }
    
    .box-selector {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        padding: 10px;
    }
    
    .box-selector::-webkit-scrollbar {
        width: 8px;
    }
    
    .box-selector::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .box-selector::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }
    
    .box-selector::-webkit-scrollbar-thumb:hover {
        background: #5568d3;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    @media (min-width: 992px) {
        .summary-card {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
    }
    
    .sticky-bottom-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 15px 20px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
        z-index: 1000;
        display: none;
    }
    
    .sticky-bottom-bar.show {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .sticky-bottom-info {
        color: white;
        display: flex;
        gap: 25px;
        align-items: center;
    }
    
    .sticky-stat {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .sticky-stat-number {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .stat-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 16px;
        border-radius: 12px;
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        display: block;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .material-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        margin: 2px;
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 600;
    }
    
    .selected-box-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }
    
    .selected-box-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="bi bi-truck text-primary me-2"></i>
                إنشاء إذن صرف منتجات نهائية
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i>
                اختر الصناديق المطلوبة للعميل - تظهر جميع الصناديق المتاحة مع مواصفاتها
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    @if($availableBoxes->isEmpty())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle fs-4 me-2"></i>
            <div>
                <strong>لا توجد صناديق في المستودع حالياً.</strong>
                <p class="mb-0">يجب إدخال الصناديق للمستودع أولاً من خلال طلبات إدخال المستودع قبل أن تظهر هنا للصرف.</p>
            </div>
        </div>
    @else
    <form id="deliveryForm" method="POST" action="{{ route('manufacturing.finished-product-deliveries.store') }}">
        @csrf
        
        <div class="row">
            <!-- قسم عرض الصناديق المتاحة -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>
                                الصناديق المتاحة للصرف
                            </h5>
                            <span class="badge bg-light text-primary">{{ $availableBoxes->count() }} صندوق</span>
                        </div>
                        <p class="mb-0 mt-2" style="font-size: 0.9rem; opacity: 0.95;">
                            <i class="bi bi-info-circle me-1"></i>
                            انقر على الصندوق لاختياره - تظهر مواصفات كل صندوق من اللون والنوع والسمك
                        </p>
                    </div>
                    <div class="card-body">
                        <div id="boxesList" class="box-selector"></div>
                    </div>
                </div>
            </div>

            <!-- قسم الملخص -->
            <div class="col-lg-4">
                <div class="summary-card mb-4">
                    <h5 class="mb-4">
                        <i class="bi bi-clipboard-check me-2"></i>
                        ملخص إذن الصرف
                    </h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="stat-badge">
                                <span class="stat-number" id="selectedCount">0</span>
                                <span class="stat-label">صندوق</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-badge">
                                <span class="stat-number" id="totalWeight">0</span>
                                <span class="stat-label">كجم</span>
                            </div>
                        </div>
                    </div>

                    <!-- العميل -->
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">العميل</label>
                        <select name="customer_id" id="customerId" class="form-select">
                            <option value="">يمكن تحديده لاحقاً من قبل المدير</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }} ({{ $customer->customer_code }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-white d-block mt-1" style="opacity: 0.85;">
                            <i class="bi bi-lightbulb me-1"></i>
                            إذا لم يتم التحديد، سيقوم المدير بتحديده عند الاعتماد
                        </small>
                    </div>

                    <!-- الملاحظات -->
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">ملاحظات (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="أضف ملاحظات حول هذا الإذن..."></textarea>
                    </div>

                    <!-- زر الحفظ -->
                    <button type="submit" id="submitBtn" class="btn btn-light w-100 py-3 fw-bold mb-2" disabled>
                        <i class="bi bi-check-circle me-1"></i>
                        حفظ إذن الصرف
                    </button>
                    
                    <button type="button" id="clearAllBoxes" class="btn btn-outline-light w-100" style="display:none;">
                        <i class="bi bi-x-circle me-1"></i>
                        إلغاء تحديد الكل
                    </button>
                </div>

                <!-- قائمة الصناديق المحددة -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-check2-square me-1"></i>
                            الصناديق المحددة
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <div id="selectedBoxesList">
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="mb-0">لم يتم اختيار صناديق بعد</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- شريط ثابت في الأسفل يظهر عند التمرير -->
    <div class="sticky-bottom-bar" id="stickyBar">
        <div class="sticky-bottom-info">
            <div class="sticky-stat">
                <i class="bi bi-box-seam fs-4"></i>
                <div>
                    <div class="sticky-stat-number" id="stickyCount">0</div>
                    <small>صندوق</small>
                </div>
            </div>
            <div class="sticky-stat">
                <i class="bi bi-speedometer fs-4"></i>
                <div>
                    <div class="sticky-stat-number" id="stickyWeight">0</div>
                    <small>كجم</small>
                </div>
            </div>
        </div>
        <button type="button" id="stickySubmitBtn" class="btn btn-light btn-lg px-5 fw-bold" disabled>
            <i class="bi bi-check-circle me-2"></i>
            حفظ إذن الصرف
        </button>
    </div>
    
    @endif
</div>

@endsection

@push('scripts')
@if(!$availableBoxes->isEmpty())
<script>
$(document).ready(function() {
    const allBoxes = @json($availableBoxes);
    let selectedBoxes = [];

    // عرض جميع الصناديق مباشرة
    displayBoxes(allBoxes);

    function displayBoxes(boxes) {
        const container = $('#boxesList');
        
        if (boxes.length === 0) {
            container.html(`
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    لا توجد صناديق متاحة
                </div>
            `);
            return;
        }

        let html = '';
        boxes.forEach(box => {
            const isSelected = selectedBoxes.some(b => b.id === box.id);
            const materials = box.materials || [];
            const materialsHtml = materials.length > 0 
                ? materials.map(m => {
                    let specs = `<span class="material-badge">
                        <i class="bi bi-palette me-1"></i>`;
                    
                    let parts = [];
                    if (m.color && m.color !== 'غير محدد') parts.push(m.color);
                    if (m.type && m.type !== 'غير محدد') parts.push(m.type);
                    if (m.wire_size) parts.push(m.wire_size);
                    if (m.plastic_type) parts.push(m.plastic_type);
                    
                    specs += parts.length > 0 ? parts.join(' - ') : 'غير محدد';
                    specs += `</span>`;
                    return specs;
                }).join('')
                : '<span class="text-muted">لا توجد مواصفات</span>';
            
            html += `
                <div class="box-card ${isSelected ? 'selected' : ''}" data-box-id="${box.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <strong class="text-primary fs-5">${box.barcode}</strong>
                                ${isSelected ? '<span class="badge bg-success ms-2"><i class="bi bi-check-circle-fill"></i> محدد</span>' : ''}
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1"><i class="bi bi-palette me-1"></i> المواصفات:</small>
                                <div>${materialsHtml}</div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-box text-muted me-1"></i>التغليف:</small><br>
                                    <strong>${box.packaging_type}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-speedometer text-muted me-1"></i>الوزن:</small><br>
                                    <strong>${parseFloat(box.weight).toFixed(2)} كجم</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-layers text-muted me-1"></i>عدد اللفات:</small><br>
                                    <strong>${box.coils_count || 0}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted"><i class="bi bi-person text-muted me-1"></i>المسؤول:</small><br>
                                    <small>${box.creator || 'غير محدد'}</small>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-primary'} toggle-box-btn" 
                                    data-box-id="${box.id}">
                                <i class="bi bi-${isSelected ? 'check-circle-fill' : 'plus-circle'}"></i>
                                ${isSelected ? ' محدد' : ' اختيار'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.html(html);
    }

    // إضافة/إزالة صندوق
    $(document).on('click', '.toggle-box-btn', function(e) {
        e.stopPropagation();
        const boxId = parseInt($(this).data('box-id'));
        const box = allBoxes.find(b => b.id === boxId);
        const isSelected = selectedBoxes.some(b => b.id === boxId);

        if (isSelected) {
            selectedBoxes = selectedBoxes.filter(b => b.id !== boxId);
        } else {
            selectedBoxes.push(box);
        }

        displayBoxes(allBoxes);
        updateSummary();
    });

    // النقر على الكارت نفسه
    $(document).on('click', '.box-card', function(e) {
        if (!$(e.target).closest('button').length) {
            $(this).find('.toggle-box-btn').click();
        }
    });

    // مسح جميع الصناديق
    $('#clearAllBoxes').on('click', function() {
        selectedBoxes = [];
        displayBoxes(allBoxes);
        updateSummary();
    });

    // تحديث الملخص
    function updateSummary() {
        const count = selectedBoxes.length;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);

        $('#selectedCount').text(count);
        $('#totalWeight').text(totalWeight.toFixed(2));

        // تحديث قائمة الصناديق المحددة
        if (count === 0) {
            $('#selectedBoxesList').html(`
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mb-0">لم يتم اختيار صناديق بعد</p>
                </div>
            `);
            $('#clearAllBoxes').hide();
        } else {
            let listHtml = '';
            selectedBoxes.forEach((box, index) => {
                const materials = box.materials || [];
                const materialsText = materials.length > 0 
                    ? materials.map(m => `${m.color}`).join(', ')
                    : 'غير محدد';
                
                listHtml += `
                    <div class="selected-box-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary">${index + 1}</span>
                                    <strong>${box.barcode}</strong>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="bi bi-palette me-1"></i>${materialsText}
                                </small>
                                <small class="text-muted">
                                    ${parseFloat(box.weight || 0).toFixed(2)} كجم • ${box.packaging_type}
                                </small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-box" data-box-id="${box.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            $('#selectedBoxesList').html(listHtml);
            $('#clearAllBoxes').show();
        }

        // تفعيل/تعطيل زر الحفظ
        $('#submitBtn').prop('disabled', count === 0);
        $('#stickySubmitBtn').prop('disabled', count === 0);
        
        // تحديث الشريط الثابت
        $('#stickyCount').text(count);
        $('#stickyWeight').text(totalWeight.toFixed(2));
    }
    
    // إظهار/إخفاء الشريط الثابت عند التمرير
    let summaryCardTop = $('.summary-card').offset().top;
    $(window).on('scroll', function() {
        let scrollTop = $(window).scrollTop();
        let windowHeight = $(window).height();
        let summaryCardBottom = summaryCardTop + $('.summary-card').outerHeight();
        
        // إظهار الشريط عندما يكون زر الحفظ خارج الشاشة
        if (scrollTop + windowHeight < summaryCardBottom || scrollTop > summaryCardTop + 200) {
            $('#stickyBar').addClass('show');
        } else {
            $('#stickyBar').removeClass('show');
        }
    });
    
    // نسخ وظيفة زر الحفظ للشريط الثابت
    $('#stickySubmitBtn').on('click', function() {
        $('#deliveryForm').submit();
    });

    // إزالة صندوق من القائمة المحددة
    $(document).on('click', '.remove-box', function(e) {
        e.stopPropagation();
        const boxId = $(this).data('box-id');
        selectedBoxes = selectedBoxes.filter(b => b.id !== boxId);
        displayBoxes(allBoxes);
        updateSummary();
    });

    // إرسال النموذج
    $('#deliveryForm').on('submit', function(e) {
        e.preventDefault();

        if (selectedBoxes.length === 0) {
            Swal.fire('تنبيه', 'يجب اختيار صندوق واحد على الأقل', 'warning');
            return;
        }

        const formData = new FormData(this);
        
        selectedBoxes.forEach((box, index) => {
            formData.append(`boxes[${index}][box_id]`, box.id);
            formData.append(`boxes[${index}][barcode]`, box.barcode);
        });

        const submitBtn = $('#submitBtn');
        const stickyBtn = $('#stickySubmitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...');
        stickyBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'نجح!',
                        text: response.message,
                        confirmButtonText: 'عرض الإذن'
                    }).then(() => {
                        window.location.href = `/finished-product-deliveries/${response.delivery_note_id}`;
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الحفظ';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: errorMessage
                });
                
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endif
@endpush
