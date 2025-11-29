@extends('master')

@section('title', 'طلب إدخال صناديق للمستودع')

@section('content')
<style>
    .box-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }
    
    .box-card:hover {
        border-color: #4CAF50;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        transform: translateY(-2px);
    }
    
    .box-card.selected {
        border-color: #4CAF50;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    
    .box-selector {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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
    
    .stat-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 15px;
        border-radius: 10px;
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
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="bi bi-box-arrow-in-down me-2 text-success"></i>
                طلب إدخال صناديق للمستودع
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i>
                اختر الصناديق الجاهزة من المرحلة الرابعة لإرسالها للمستودع
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.warehouse-intake.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة
            </a>
        </div>
    </div>

    <form id="intakeForm" method="POST" action="{{ route('manufacturing.warehouse-intake.store') }}">
        @csrf
        
        <div class="row">
            <!-- قسم البحث والاختيار -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-search me-2"></i>
                            الصناديق المتاحة من المرحلة الرابعة
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- شريط البحث -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">البحث بالباركود</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" id="searchBarcode" class="form-control" 
                                       placeholder="امسح أو أدخل الباركود..." autofocus>
                                <button type="button" id="searchBoxes" class="btn btn-success">
                                    <i class="bi bi-search me-1"></i>
                                    بحث
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-lightbulb me-1"></i>
                                امسح الباركود أو اضغط Enter للبحث - سيتم إضافة الصندوق تلقائياً
                            </small>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع التغليف</label>
                                <select id="filterPackaging" class="form-select">
                                    <option value="">جميع الأنواع</option>
                                    <option value="صندوق">صندوق</option>
                                    <option value="شوال">شوال</option>
                                    <option value="كيس">كيس</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">النتائج</label>
                                <input type="text" id="resultsCount" class="form-control" readonly value="0 صندوق">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="addAllBoxes" class="btn btn-outline-success" style="display:none;">
                                <i class="bi bi-plus-circle me-1"></i>
                                إضافة جميع النتائج
                            </button>
                            <button type="button" id="clearSearch" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                مسح البحث
                            </button>
                        </div>

                        <!-- قائمة الصناديق -->
                        <div id="boxesList" class="box-selector">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-search me-2" style="font-size: 2rem;"></i>
                                <p class="mb-0"><strong>ابدأ البحث</strong> عن الصناديق المتاحة</p>
                                <small>يمكنك مسح الباركود مباشرة أو البحث بالرقم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم الملخص -->
            <div class="col-lg-4">
                <div class="summary-card mb-4">
                    <h5 class="mb-4">
                        <i class="bi bi-clipboard-check me-2"></i>
                        ملخص الطلب
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

                    <div class="mb-3">
                        <label class="form-label text-white">ملاحظات (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="أضف أي ملاحظات عن هذا الطلب..."></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-light w-100 py-3 fw-bold" disabled>
                        <i class="bi bi-send-fill me-2"></i>
                        إرسال طلب الإدخال
                    </button>
                </div>

                <!-- الصناديق المحددة -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-box-seam me-1"></i>
                                الصناديق المحددة
                            </h6>
                            <button type="button" id="clearAllBoxes" class="btn btn-sm btn-outline-danger" style="display:none;">
                                <i class="bi bi-trash me-1"></i>
                                مسح الكل
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div id="selectedBoxesList">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">لم يتم اختيار صناديق</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedBoxes = [];
    let currentBoxes = [];

    // البحث عند الضغط على Enter
    $('#searchBarcode').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#searchBoxes').click();
        }
    });

    // البحث عن الصناديق
    $('#searchBoxes').on('click', function() {
        const barcode = $('#searchBarcode').val().trim();
        const packaging = $('#filterPackaging').val();

        if (!barcode && !packaging) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'الرجاء إدخال باركود أو اختيار نوع التغليف',
                timer: 2000
            });
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.warehouse-intake.api.available-boxes") }}',
            method: 'GET',
            data: {
                search: barcode,
                packaging_type: packaging
            },
            success: function(boxes) {
                if (boxes.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'لا توجد نتائج',
                        text: 'لم يتم العثور على صناديق متطابقة',
                        timer: 2000
                    });
                    return;
                }

                // إذا كان البحث بباركود ونتيجة واحدة، أضفها تلقائياً
                if (barcode && boxes.length === 1) {
                    const box = boxes[0];
                    const isAlreadyAdded = selectedBoxes.some(b => b.id === box.id);
                    
                    if (isAlreadyAdded) {
                        Swal.fire({
                            icon: 'info',
                            title: 'تم الإضافة مسبقاً',
                            text: `الصندوق ${box.barcode} موجود بالفعل`,
                            timer: 1500
                        });
                    } else {
                        selectedBoxes.push(box);
                        updateSummary();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت الإضافة!',
                            html: `<strong>${box.barcode}</strong><br>${parseFloat(box.weight).toFixed(2)} كجم`,
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                    
                    $('#searchBarcode').val('').focus();
                } else {
                    currentBoxes = boxes;
                    displayBoxes(boxes);
                    $('#resultsCount').val(boxes.length + ' صندوق');
                    $('#addAllBoxes').show();
                }
            },
            error: function() {
                Swal.fire('خطأ', 'فشل تحميل الصناديق', 'error');
            }
        });
    });

    // عرض الصناديق
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
            html += `
                <div class="box-card ${isSelected ? 'selected' : ''}" data-box-id="${box.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <strong class="text-primary fs-5">${box.barcode}</strong>
                                ${isSelected ? '<span class="badge bg-success ms-2"><i class="bi bi-check-circle"></i> محدد</span>' : ''}
                            </div>
                            <div class="row g-2 small">
                                <div class="col-4">
                                    <i class="bi bi-box text-muted me-1"></i>
                                    <strong>${box.packaging_type}</strong>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-speedometer text-muted me-1"></i>
                                    <strong>${parseFloat(box.weight).toFixed(2)} كجم</strong>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-layers text-muted me-1"></i>
                                    <strong>${box.coils_count || 0} لفة</strong>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-${isSelected ? 'success' : 'outline-success'} btn-sm add-box-btn" 
                                    data-box='${JSON.stringify(box)}'>
                                <i class="bi bi-${isSelected ? 'check-circle-fill' : 'plus-circle'}"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.html(html);
    }

    // إضافة/إزالة صندوق
    $(document).on('click', '.add-box-btn', function(e) {
        e.stopPropagation();
        const box = JSON.parse($(this).attr('data-box'));
        const isSelected = selectedBoxes.some(b => b.id === box.id);

        if (isSelected) {
            selectedBoxes = selectedBoxes.filter(b => b.id !== box.id);
        } else {
            selectedBoxes.push(box);
        }

        displayBoxes(currentBoxes);
        updateSummary();
    });

    // إضافة جميع الصناديق
    $('#addAllBoxes').on('click', function() {
        currentBoxes.forEach(box => {
            const isSelected = selectedBoxes.some(b => b.id === box.id);
            if (!isSelected) {
                selectedBoxes.push(box);
            }
        });
        displayBoxes(currentBoxes);
        updateSummary();
        
        Swal.fire({
            icon: 'success',
            title: 'تمت الإضافة!',
            text: `تم إضافة ${currentBoxes.length} صندوق`,
            timer: 1500
        });
    });

    // مسح البحث
    $('#clearSearch').on('click', function() {
        $('#searchBarcode').val('').focus();
        $('#filterPackaging').val('');
        $('#resultsCount').val('0 صندوق');
        $('#addAllBoxes').hide();
        $('#boxesList').html(`
            <div class="alert alert-info text-center">
                <i class="bi bi-search me-2" style="font-size: 2rem;"></i>
                <p class="mb-0"><strong>ابدأ البحث</strong> عن الصناديق المتاحة</p>
            </div>
        `);
        currentBoxes = [];
    });

    // تحديث الملخص
    function updateSummary() {
        const count = selectedBoxes.length;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);

        $('#selectedCount').text(count);
        $('#totalWeight').text(totalWeight.toFixed(2));

        if (count === 0) {
            $('#selectedBoxesList').html(`
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mb-0">لم يتم اختيار صناديق</p>
                </div>
            `);
            $('#clearAllBoxes').hide();
            $('#submitBtn').prop('disabled', true);
        } else {
            let listHtml = '';
            selectedBoxes.forEach((box, index) => {
                listHtml += `
                    <div class="selected-box-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary">${index + 1}</span>
                                    <strong>${box.barcode}</strong>
                                </div>
                                <small class="text-muted">
                                    ${parseFloat(box.weight).toFixed(2)} كجم • ${box.packaging_type}
                                </small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-box" data-box-id="${box.id}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            $('#selectedBoxesList').html(listHtml);
            $('#clearAllBoxes').show();
            $('#submitBtn').prop('disabled', false);
        }
    }

    // إزالة صندوق
    $(document).on('click', '.remove-box', function(e) {
        e.stopPropagation();
        const boxId = $(this).data('box-id');
        selectedBoxes = selectedBoxes.filter(b => b.id !== boxId);
        displayBoxes(currentBoxes);
        updateSummary();
    });

    // مسح جميع الصناديق
    $('#clearAllBoxes').on('click', function() {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم إزالة جميع الصناديق المحددة',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، امسح الكل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedBoxes = [];
                displayBoxes(currentBoxes);
                updateSummary();
            }
        });
    });

    // إرسال النموذج
    $('#intakeForm').on('submit', function(e) {
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
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i> جاري الإرسال...');

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
                        title: 'تم الإرسال بنجاح!',
                        html: `<p>${response.message}</p><p class="mb-0"><strong>رقم الطلب:</strong> ${response.request_number}</p>`,
                        confirmButtonText: 'عرض الطلب'
                    }).then(() => {
                        window.location.href = `/warehouse-intake/${response.request_id}`;
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الإرسال';
                
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
@endpush
