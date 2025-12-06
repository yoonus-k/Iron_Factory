@extends('master')

@section('title', __('app.finished_products.create_delivery_note'))

@section('content')
<style>
    .box-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .box-card:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .box-card.selected {
        border-color: #667eea;
        background-color: #f0f4ff;
    }

    .box-selector {
        max-height: 500px;
        overflow-y: auto;
    }

    .selected-boxes-summary {
        background: #e8f5e9;
        border: 2px solid #4caf50;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>
                {{ __('app.finished_products.create_delivery_note') }}
            </h2>
            <p class="text-muted mb-0">{{ __('app.finished_products.select_boxes_stage4') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                {{ __('app.finished_products.back_to_list') }}
            </a>
        </div>
    </div>

    <form id="deliveryForm" method="POST" action="{{ route('manufacturing.finished-product-deliveries.store') }}">
        @csrf

        <div class="row">
            <!-- قسم البحث واختيار الصناديق -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-search me-2"></i>
                            {{ __('app.finished_products.search_available_boxes') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.search_by_barcode') }}</label>
                            <div class="input-group">
                                <input type="text" id="searchBarcode" class="form-control"
                                       placeholder="{{ __('app.finished_products.barcode_search_placeholder') }}">
                                <button type="button" id="searchBoxes" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>
                                    {{ __('app.buttons.search') }}
                                </button>
                                <button type="button" id="addDirectly" class="btn btn-success" style="display:none;">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    {{ __('app.buttons.add_all') }}
                                </button>
                                <button type="button" id="clearSearch" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>
                                    {{ __('app.buttons.clear') }}
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.finished_products.packaging_type') }}</label>
                                <select id="filterPackaging" class="form-select">
                                    <option value="">{{ __('app.buttons.all') }}</option>
                                    <option value="صندوق">{{ __('app.finished_products.type_box') }}</option>
                                    <option value="شوال">{{ __('app.finished_products.type_sack') }}</option>
                                    <option value="كيس">{{ __('app.finished_products.type_bag') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.finished_products.results_count') }}</label>
                                <input type="text" id="resultsCount" class="form-control" readonly value="0 {{ __('app.finished_products.boxes') }}">
                            </div>
                        </div>

                        <div id="boxesList" class="box-selector">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('app.finished_products.search_tip') }}:</strong> {{ __('app.finished_products.search_will_add_auto') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم الملخص والحفظ -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            {{ __('app.finished_products.note_summary') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- العميل (اختياري) -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.customer') }} ({{ __('app.finished_products.optional') }})</label>
                            <select name="customer_id" id="customerId" class="form-select">
                                <option value="">{{ __('app.finished_products.can_be_set_later') }}</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} ({{ $customer->customer_code }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('app.finished_products.customer_note_on_approval') }}</small>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="{{ __('app.finished_products.notes_placeholder') }}"></textarea>
                        </div>

                        <!-- ملخص الصناديق المحددة -->
                        <div id="selectedSummary" class="alert alert-secondary">
                            <h6 class="mb-2">{{ __('app.finished_products.selected_boxes') }}:</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ __('app.finished_products.count') }}:</span>
                                <strong id="selectedCount">0</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ __('app.finished_products.total_weight_label') }}:</span>
                                <strong id="totalWeight">0.00 {{ __('app.units.kg') }}</strong>
                            </div>
                        </div>

                        <!-- قائمة الصناديق المحددة -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ __('app.finished_products.selected_boxes') }}:</h6>
                                <button type="button" id="clearAllBoxes" class="btn btn-sm btn-outline-danger" style="display:none;">
                                    <i class="bi bi-trash me-1"></i>
                                    {{ __('app.buttons.clear_all') }}
                                </button>
                            </div>
                            <div id="selectedBoxesList" style="max-height: 300px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <p class="mb-0">{{ __('app.finished_products.no_boxes_selected') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ -->
                        <button type="submit" id="submitBtn" class="btn btn-success w-100 mb-2" disabled>
                            <i class="bi bi-check-circle me-1"></i>
                            {{ __('app.buttons.save') }}
                        </button>

                        <a href="{{ route('manufacturing.finished-product-deliveries.index') }}"
                           class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>
                            {{ __('app.buttons.cancel') }}
                        </a>
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
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.enter_barcode_or_select_filter") }}', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.finished-product-deliveries.api.available-boxes") }}',
            method: 'GET',
            data: {
                search: barcode,
                packaging_type: packaging
            },
            success: function(boxes) {
                if (boxes.length === 0) {
                    Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.no_matching_boxes") }}', 'info');
                    return;
                }

                // إذا كان البحث بباركود محدد ونتيجة واحدة، أضفها مباشرة
                if (barcode && boxes.length === 1) {
                    const box = boxes[0];
                    const isAlreadyAdded = selectedBoxes.some(b => b.id === box.id);

                    if (isAlreadyAdded) {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __("app.finished_products.already_added") }}',
                            text: `{{ __("app.finished_products.box") }} ${box.barcode} {{ __("app.finished_products.already_in_list") }}`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // إضافة الصندوق مباشرة
                        selectedBoxes.push(box);
                        updateSummary();

                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("app.finished_products.added") }}!',
                            text: `{{ __("app.finished_products.added_successfully") }} ${box.barcode}`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }

                    // مسح حقل البحث
                    $('#searchBarcode').val('').focus();
                } else {
                    // إذا كانت نتائج متعددة، اعرضها
                    currentBoxes = boxes;
                    displayBoxes(boxes);
                    $('#resultsCount').val(boxes.length + ' {{ __("app.finished_products.boxes") }}');

                    if (boxes.length > 0) {
                        $('#addDirectly').show();
                    } else {
                        $('#addDirectly').hide();
                    }
                }
            },
            error: function(xhr) {
                Swal.fire('{{ __("app.finished_products.error") }}', '{{ __("app.finished_products.failed_load_boxes") }}', 'error');
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
                    {{ __('app.finished_products.no_available_boxes') }}
                </div>
            `);
            return;
        }

        let html = '';
        boxes.forEach(box => {
            const isSelected = selectedBoxes.some(b => b.id === box.id);
            const boxJson = JSON.stringify(box).replace(/'/g, "&apos;");
            html += `
                <div class="box-card ${isSelected ? 'selected' : ''}" data-box-id="${box.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <strong class="text-primary">${box.barcode}</strong>
                                ${isSelected ? '<span class="badge bg-success ms-2">✓ {{ __("app.finished_products.selected") }}</span>' : ''}
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">{{ __('app.finished_products.packaging_type') }}:</small><br>
                                    <strong>${box.packaging_type}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">{{ __('app.finished_products.weight') }}:</small><br>
                                    <strong>${parseFloat(box.weight).toFixed(2)} {{ __('app.units.kg') }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">{{ __('app.finished_products.coils_count') }}:</small><br>
                                    <strong>${box.coils_count || 0}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">{{ __('app.finished_products.worker') }}:</small><br>
                                    <small>${box.worker ? box.worker.name : '-'}</small>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-primary'} add-box-btn" data-box='${boxJson}'>
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
        const box = JSON.parse($(this).attr('data-box').replace(/&apos;/g, "'"));
        const isSelected = selectedBoxes.some(b => b.id === box.id);

        if (isSelected) {
            // إزالة من القائمة
            selectedBoxes = selectedBoxes.filter(b => b.id !== box.id);
            $(this).removeClass('btn-success').addClass('btn-outline-primary');
            $(this).html('<i class="bi bi-plus-circle"></i>');
            $(this).closest('.box-card').removeClass('selected');
            $(this).closest('.box-card').find('.badge').remove();
        } else {
            // إضافة للقائمة
            selectedBoxes.push(box);
            $(this).removeClass('btn-outline-primary').addClass('btn-success');
            $(this).html('<i class="bi bi-check-circle-fill"></i>');
            $(this).closest('.box-card').addClass('selected');

            const barcodDiv = $(this).closest('.box-card').find('.text-primary');
            if (barcodDiv.next('.badge').length === 0) {
                barcodDiv.after('<span class="badge bg-success ms-2">✓ {{ __("app.finished_products.selected") }}</span>');
            }
        }

        updateSummary();
    });

    // إضافة جميع الصناديق الظاهرة
    $('#addDirectly').on('click', function() {
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
            title: '{{ __("app.finished_products.added") }}',
            text: `{{ __("app.finished_products.added_boxes_count") }} ${currentBoxes.length} {{ __("app.finished_products.boxes") }}`,
            timer: 1500,
            showConfirmButton: false
        });
    });

    // مسح البحث
    $('#clearSearch').on('click', function() {
        $('#searchBarcode').val('').focus();
        $('#filterPackaging').val('');
        $('#resultsCount').val('0 {{ __("app.finished_products.boxes") }}');
        $('#addDirectly').hide();
        $('#boxesList').html(`
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <strong>{{ __('app.finished_products.search_tip') }}:</strong> {{ __('app.finished_products.search_will_add_auto') }}
            </div>
        `);
        currentBoxes = [];
    });

    // تحديث الملخص
    function updateSummary() {
        const count = selectedBoxes.length;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);

        $('#selectedCount').text(count);
        $('#totalWeight').text(totalWeight.toFixed(2) + ' {{ __("app.units.kg") }}');

        // تحديث قائمة الصناديق المحددة
        if (count === 0) {
            $('#selectedBoxesList').html(`
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0">{{ __('app.finished_products.no_boxes_selected') }}</p>
                </div>
            `);
            $('#clearAllBoxes').hide();
        } else {
            let listHtml = '';
            selectedBoxes.forEach((box, index) => {
                listHtml += `
                    <div class="alert alert-light py-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary">${index + 1}</span>
                                    <strong>${box.barcode}</strong>
                                </div>
                                <small class="text-muted">${parseFloat(box.weight || 0).toFixed(2)} {{ __('app.units.kg') }} | ${box.packaging_type}</small>
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
    }

    // إزالة صندوق من القائمة المحددة
    $(document).on('click', '.remove-box', function(e) {
        e.stopPropagation();
        const boxId = $(this).data('box-id');
        selectedBoxes = selectedBoxes.filter(b => b.id !== boxId);

        // تحديث عرض الصندوق في نتائج البحث
        $(`.add-box-btn[data-box*='"id":${boxId}']`)
            .removeClass('btn-success')
            .addClass('btn-outline-primary')
            .html('<i class="bi bi-plus-circle"></i>');

        $(`.box-card[data-box-id="${boxId}"]`).removeClass('selected').find('.badge').remove();

        updateSummary();
    });

    // مسح جميع الصناديق المحددة
    $('#clearAllBoxes').on('click', function() {
        Swal.fire({
            title: '{{ __("app.finished_products.are_you_sure") }}?',
            text: '{{ __("app.finished_products.will_remove_all_boxes") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("app.finished_products.yes_clear_all") }}',
            cancelButtonText: '{{ __("app.buttons.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedBoxes = [];
                $('.add-box-btn').removeClass('btn-success').addClass('btn-outline-primary')
                    .html('<i class="bi bi-plus-circle"></i>');
                $('.box-card').removeClass('selected').find('.badge').remove();
                updateSummary();
            }
        });
    });

    // إرسال النموذج
    $('#deliveryForm').on('submit', function(e) {
        e.preventDefault();

        if (selectedBoxes.length === 0) {
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.select_at_least_one_box") }}', 'warning');
            return;
        }

        const formData = new FormData(this);

        // إضافة الصناديق المحددة
        selectedBoxes.forEach((box, index) => {
            formData.append(`boxes[${index}][box_id]`, box.id);
            formData.append(`boxes[${index}][barcode]`, box.barcode);
        });

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> {{ __("app.finished_products.saving") }}...');

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
                        title: '{{ __("app.finished_products.success") }}!',
                        text: response.message,
                        confirmButtonText: '{{ __("app.finished_products.view_note") }}'
                    }).then(() => {
                        window.location.href = `/finished-product-deliveries/${response.delivery_note_id}`;
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = '{{ __("app.finished_products.error_saving") }}';

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.finished_products.error") }}',
                    text: errorMessage
                });

                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush

    <form id="deliveryForm" method="POST" action="{{ route('manufacturing.finished-product-deliveries.store') }}">
        @csrf

        <div class="row">
            <!-- قسم البحث واختيار الصناديق -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-search me-2"></i>
                            البحث عن الصناديق المتاحة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">البحث بالباركود</label>
                            <div class="input-group">
                                <input type="text" id="searchBarcode" class="form-control"
                                       placeholder="أدخل الباركود واضغط Enter أو انقر بحث">
                                <button type="button" id="searchBoxes" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>
                                    بحث
                                </button>
                                <button type="button" id="addDirectly" class="btn btn-success" style="display:none;">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    إضافة الكل
                                </button>
                                <button type="button" id="clearSearch" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>
                                    مسح
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع التغليف</label>
                                <select id="filterPackaging" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="صندوق">صندوق</option>
                                    <option value="شوال">شوال</option>
                                    <option value="كيس">كيس</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد النتائج</label>
                                <input type="text" id="resultsCount" class="form-control" readonly value="0 صندوق">
                            </div>
                        </div>

                        <div id="boxesList" class="box-selector">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>نصيحة:</strong> ابحث عن باركود محدد وسيتم إضافته تلقائياً للقائمة
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم الملخص والحفظ -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            ملخص الإذن
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- العميل (اختياري) -->
                        <div class="mb-3">
                            <label class="form-label">العميل (اختياري)</label>
                            <select name="customer_id" id="customerId" class="form-select">
                                <option value="">يمكن تحديده لاحقاً من قبل المدير</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} ({{ $customer->customer_code }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">إذا لم يتم التحديد، سيقوم المدير بتحديده عند الاعتماد</small>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="أضف ملاحظات إن وجدت"></textarea>
                        </div>

                        <!-- ملخص الصناديق المحددة -->
                        <div id="selectedSummary" class="alert alert-secondary">
                            <h6 class="mb-2">الصناديق المحددة:</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>العدد:</span>
                                <strong id="selectedCount">0</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>الوزن الإجمالي:</span>
                                <strong id="totalWeight">0.00 كجم</strong>
                            </div>
                        </div>

                        <!-- قائمة الصناديق المحددة -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">الصناديق المحددة:</h6>
                                <button type="button" id="clearAllBoxes" class="btn btn-sm btn-outline-danger" style="display:none;">
                                    <i class="bi bi-trash me-1"></i>
                                    مسح الكل
                                </button>
                            </div>
                            <div id="selectedBoxesList" style="max-height: 300px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <p class="mb-0">لم يتم اختيار صناديق بعد</p>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ -->
                        <button type="submit" id="submitBtn" class="btn btn-success w-100 mb-2" disabled>
                            <i class="bi bi-check-circle me-1"></i>
                            حفظ إذن الصرف
                        </button>

                        <a href="{{ route('manufacturing.finished-product-deliveries.index') }}"
                           class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>
                            إلغاء
                        </a>
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
            Swal.fire('تنبيه', 'الرجاء إدخال باركود أو اختيار نوع التغليف للبحث', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.finished-product-deliveries.api.available-boxes") }}',
            method: 'GET',
            data: {
                search: barcode,
                packaging_type: packaging
            },
            success: function(boxes) {
                if (boxes.length === 0) {
                    Swal.fire('تنبيه', 'لا توجد صناديق متطابقة', 'info');
                    return;
                }

                // إذا كان البحث بباركود محدد ونتيجة واحدة، أضفها مباشرة
                if (barcode && boxes.length === 1) {
                    const box = boxes[0];
                    const isAlreadyAdded = selectedBoxes.some(b => b.id === box.id);

                    if (isAlreadyAdded) {
                        Swal.fire({
                            icon: 'info',
                            title: 'تم الإضافة مسبقاً',
                            text: `الصندوق ${box.barcode} موجود بالفعل في القائمة`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // إضافة الصندوق مباشرة
                        selectedBoxes.push(box);
                        updateSummary();

                        Swal.fire({
                            icon: 'success',
                            title: 'تمت الإضافة!',
                            text: `تم إضافة ${box.barcode} بنجاح`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }

                    // مسح حقل البحث
                    $('#searchBarcode').val('').focus();
                } else {
                    // إذا كانت نتائج متعددة، اعرضها
                    currentBoxes = boxes;
                    displayBoxes(boxes);
                    $('#resultsCount').val(boxes.length + ' صندوق');

                    if (boxes.length > 0) {
                        $('#addDirectly').show();
                    } else {
                        $('#addDirectly').hide();
                    }
                }
            },
            error: function(xhr) {
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
            const boxJson = JSON.stringify(box).replace(/'/g, "&apos;");
            html += `
                <div class="box-card ${isSelected ? 'selected' : ''}" data-box-id="${box.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <strong class="text-primary">${box.barcode}</strong>
                                ${isSelected ? '<span class="badge bg-success ms-2">✓ محدد</span>' : ''}
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">التغليف:</small><br>
                                    <strong>${box.packaging_type}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">الوزن:</small><br>
                                    <strong>${parseFloat(box.weight).toFixed(2)} كجم</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">عدد اللفات:</small><br>
                                    <strong>${box.coils_count || 0}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">العامل:</small><br>
                                    <small>${box.worker ? box.worker.name : '-'}</small>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-primary'} add-box-btn" data-box='${boxJson}'>
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
        const box = JSON.parse($(this).attr('data-box').replace(/&apos;/g, "'"));
        const isSelected = selectedBoxes.some(b => b.id === box.id);

        if (isSelected) {
            // إزالة من القائمة
            selectedBoxes = selectedBoxes.filter(b => b.id !== box.id);
            $(this).removeClass('btn-success').addClass('btn-outline-primary');
            $(this).html('<i class="bi bi-plus-circle"></i>');
            $(this).closest('.box-card').removeClass('selected');
            $(this).closest('.box-card').find('.badge').remove();
        } else {
            // إضافة للقائمة
            selectedBoxes.push(box);
            $(this).removeClass('btn-outline-primary').addClass('btn-success');
            $(this).html('<i class="bi bi-check-circle-fill"></i>');
            $(this).closest('.box-card').addClass('selected');

            const barcodDiv = $(this).closest('.box-card').find('.text-primary');
            if (barcodDiv.next('.badge').length === 0) {
                barcodDiv.after('<span class="badge bg-success ms-2">✓ محدد</span>');
            }
        }

        updateSummary();
    });

    // إضافة جميع الصناديق الظاهرة
    $('#addDirectly').on('click', function() {
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
            title: 'تمت الإضافة',
            text: `تم إضافة ${currentBoxes.length} صندوق`,
            timer: 1500,
            showConfirmButton: false
        });
    });

    // مسح البحث
    $('#clearSearch').on('click', function() {
        $('#searchBarcode').val('').focus();
        $('#filterPackaging').val('');
        $('#resultsCount').val('0 صندوق');
        $('#addDirectly').hide();
        $('#boxesList').html(`
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <strong>نصيحة:</strong> ابحث عن باركود محدد وسيتم إضافته تلقائياً للقائمة
            </div>
        `);
        currentBoxes = [];
    });

    // تحديث الملخص
    function updateSummary() {
        const count = selectedBoxes.length;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);

        $('#selectedCount').text(count);
        $('#totalWeight').text(totalWeight.toFixed(2) + ' كجم');

        // تحديث قائمة الصناديق المحددة
        if (count === 0) {
            $('#selectedBoxesList').html(`
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0">لم يتم اختيار صناديق بعد</p>
                </div>
            `);
            $('#clearAllBoxes').hide();
        } else {
            let listHtml = '';
            selectedBoxes.forEach((box, index) => {
                listHtml += `
                    <div class="alert alert-light py-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary">${index + 1}</span>
                                    <strong>${box.barcode}</strong>
                                </div>
                                <small class="text-muted">${parseFloat(box.weight || 0).toFixed(2)} كجم | ${box.packaging_type}</small>
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
    }

    // إزالة صندوق من القائمة المحددة
    $(document).on('click', '.remove-box', function(e) {
        e.stopPropagation();
        const boxId = $(this).data('box-id');
        selectedBoxes = selectedBoxes.filter(b => b.id !== boxId);

        // تحديث عرض الصندوق في نتائج البحث
        $(`.add-box-btn[data-box*='"id":${boxId}']`)
            .removeClass('btn-success')
            .addClass('btn-outline-primary')
            .html('<i class="bi bi-plus-circle"></i>');

        $(`.box-card[data-box-id="${boxId}"]`).removeClass('selected').find('.badge').remove();

        updateSummary();
    });

    // مسح جميع الصناديق المحددة
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
                $('.add-box-btn').removeClass('btn-success').addClass('btn-outline-primary')
                    .html('<i class="bi bi-plus-circle"></i>');
                $('.box-card').removeClass('selected').find('.badge').remove();
                updateSummary();
            }
        });
    });

    // إرسال النموذج
    $('#deliveryForm').on('submit', function(e) {
        e.preventDefault();

        if (selectedBoxes.length === 0) {
            Swal.fire('تنبيه', 'يجب اختيار صندوق واحد على الأقل', 'warning');
            return;
        }

        const formData = new FormData(this);

        // إضافة الصناديق المحددة
        selectedBoxes.forEach((box, index) => {
            formData.append(`boxes[${index}][box_id]`, box.id);
            formData.append(`boxes[${index}][barcode]`, box.barcode);
        });

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...');

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

    // تحميل أنواع المنتجات للفلتر (اختياري)
    // يمكن إضافة endpoint لتحميل أنواع المنتجات
});
</script>
@endpush
