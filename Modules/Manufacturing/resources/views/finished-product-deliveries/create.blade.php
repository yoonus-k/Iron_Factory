@extends('master')

@section('title', __('app.finished_products.create_delivery_note'))

@section('content')
<style>
    .box-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 18px;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
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
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .box-card.selected {
        border-color: #27ae60;
        background: linear-gradient(135deg, #f0fff4 0%, #e6f9ea 100%);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.3);
        border-color: #667eea;
        background-color: #f0f4ff;
    }

    .box-selector {
        max-height: calc(100vh - 250px);
        max-height: 500px;
        overflow-y: auto;
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
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #27ae60;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.15);
        transition: all 0.3s ease;
    }
    
    .selected-box-item:hover {
        transform: translateX(-5px);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.25);
    }
    
    .selected-box-item strong {
        color: #27ae60;
        font-size: 1.1rem;
    }
    
    .selected-box-item .badge {
        font-size: 0.9rem;
        padding: 6px 12px;
    }

    .selected-boxes-summary {
        background: #e8f5e9;
        border: 2px solid #4caf50;
        border-radius: 10px;
        padding: 20px;
    }

    /* تحسين أنماط الأزرار */
    .btn {
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 6px;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    
    .btn i {
        font-size: 1.1em;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    .btn-primary:hover {
        background: #5568d3;
        border-color: #5568d3;
        transform: translateY(-1px);
    }
    
    .btn-success {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }
    
    .btn-success:hover {
        background: #229954;
        border-color: #229954;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: #95a5a6;
        color: white;
        border-color: #95a5a6;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
        border-color: #7f8c8d;
        transform: translateY(-1px);
    }
    
    .btn-danger {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }
    
    .btn-danger:hover {
        background: #c0392b;
        border-color: #c0392b;
        transform: translateY(-1px);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .input-group .btn {
        padding: 10px 18px;
        font-size: 0.95rem;
    }

    /* عرض البيانات المدخلة */
    .delivery-info-display {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
        display: none;
    }
    
    .delivery-info-display.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .delivery-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        border-bottom: 1px solid rgba(33, 150, 243, 0.2);
    }
    
    .delivery-info-item:last-child {
        border-bottom: none;
    }
    
    .delivery-info-item i {
        color: #1976d2;
        font-size: 1.2em;
    }
    
    .delivery-info-label {
        font-weight: 600;
        color: #1565c0;
        min-width: 80px;
    }
    
    .delivery-info-value {
        color: #0d47a1;
        font-weight: 700;
        font-size: 1.05rem;
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

        @php
            $materialTypes = collect($availableBoxes)->flatMap(function ($box) {
                return collect($box['materials'] ?? [])->pluck('type');
            })->filter(function ($value) {
                return $value && $value !== 'غير محدد';
            })->unique()->sort()->values();

            $materialColors = collect($availableBoxes)->flatMap(function ($box) {
                return collect($box['materials'] ?? [])->pluck('color');
            })->filter(function ($value) {
                return $value && $value !== 'غير محدد';
            })->unique()->sort()->values();
        @endphp

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
                                <input type="text" id="searchBarcode" class="form-control form-control-lg"
                                       placeholder="{{ __('app.finished_products.barcode_search_placeholder') }}">
                                <button type="button" id="searchBoxes" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                    <span>{{ __('app.buttons.search') }}</span>
                                </button>
                                <button type="button" id="addDirectly" class="btn btn-success" style="display:none;">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>{{ __('app.buttons.add_all') }}</span>
                                </button>
                                <button type="button" id="clearSearch" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i>
                                    <span>{{ __('app.buttons.clear') }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- بحث حسب الصنف واللون والعدد -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('app.finished_products.material_type_filter') }}</label>
                                <select id="filterMaterialType" class="form-select">
                                    <option value="">{{ __('app.buttons.all') }}</option>
                                    @foreach($materialTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('app.finished_products.color_filter') }}</label>
                                <select id="filterColor" class="form-select">
                                    <option value="">{{ __('app.buttons.all') }}</option>
                                    @foreach($materialColors as $color)
                                        <option value="{{ $color }}">{{ $color }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('app.finished_products.quantity_filter') }}</label>
                                <div class="input-group">
                                    <input type="number" id="selectQuantity" class="form-control" min="1" value="1">
                                    <button type="button" id="addByTypeColor" class="btn btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        {{ __('app.finished_products.add_button') }}
                                    </button>
                                </div>
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

                <!-- الصناديق المحددة -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>
                                {{ __('app.finished_products.selected_boxes') }}
                            </h5>
                            <button type="button" id="clearAllBoxes" class="btn btn-sm btn-danger" style="display:none;">
                                <i class="bi bi-trash me-1"></i>
                                {{ __('app.buttons.clear_all') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ملخص الصناديق المحددة -->
                        <div id="selectedSummary" class="alert alert-secondary mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="bi bi-box-seam me-1"></i>{{ __('app.finished_products.count') }}:</span>
                                <strong id="selectedCount">0</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-speedometer me-1"></i>{{ __('app.finished_products.total_weight_label') }}:</span>
                                <strong id="totalWeight">0.00 {{ __('app.units.kg') }}</strong>
                            </div>
                        </div>

                        <!-- قائمة الصناديق -->
                        <div id="selectedBoxesList" style="max-height: 400px; overflow-y: auto;">
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0">{{ __('app.finished_products.no_boxes_selected') }}</p>
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

                        <!-- معلومات التوصيل -->
                        <div class="card mb-3 border-info">
                            <div class="card-header bg-info bg-opacity-10">
                                <h6 class="mb-0">
                                    <i class="bi bi-truck me-2"></i>
                                    {{ __('app.finished_products.delivery_info_optional') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- اسم السائق -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ __('app.finished_products.driver_name_label') }}
                                    </label>
                                    <input type="text" name="driver_name" id="driverName" class="form-control" placeholder="{{ __('app.finished_products.driver_name_placeholder') }}">
                                </div>

                                <!-- رقم اللوحة -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-card-text me-1"></i>
                                        {{ __('app.finished_products.vehicle_number_label') }}
                                    </label>
                                    <input type="text" name="vehicle_number" id="vehicleNumber" class="form-control" placeholder="{{ __('app.finished_products.vehicle_number_placeholder') }}">
                                </div>

                                <!-- المدينة -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ __('app.finished_products.city_destination_label') }}
                                    </label>
                                    <input type="text" name="city" id="cityName" class="form-control" placeholder="{{ __('app.finished_products.city_destination_placeholder') }}">
                                </div>

                                <!-- عرض البيانات المدخلة -->
                                <div id="deliveryInfoDisplay" class="delivery-info-display">
                                    <div class="text-center mb-2">
                                        <strong style="color: #1565c0;">
                                            <i class="bi bi-info-circle me-1"></i>
                                            {{ __('app.finished_products.entered_data') }}
                                        </strong>
                                    </div>
                                    <div id="deliveryInfoContent"></div>
                                </div>
                            </div>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="{{ __('app.finished_products.notes_placeholder') }}"></textarea>
                        </div>

                        <!-- أزرار الحفظ -->
                        <button type="submit" id="submitBtn" class="btn btn-success w-100 mb-2 py-3" disabled>
                            <i class="bi bi-check-circle"></i>
                            <span style="font-size: 1.1rem;">{{ __('app.buttons.save') }}</span>
                        </button>

                        <a href="{{ route('manufacturing.finished-product-deliveries.index') }}"
                           class="btn btn-secondary w-100 py-2">
                            <i class="bi bi-x-circle"></i>
                            <span>{{ __('app.buttons.cancel') }}</span>
                        </a>
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
                    <small>{{ __('app.finished_products.box') }}</small>
                </div>
            </div>
            <div class="sticky-stat">
                <i class="bi bi-speedometer fs-4"></i>
                <div>
                    <div class="sticky-stat-number" id="stickyWeight">0</div>
                    <small>{{ __('app.units.kg') }}</small>
                </div>
            </div>
        </div>
        <button type="button" id="stickySubmitBtn" class="btn btn-light btn-lg px-5 fw-bold" disabled>
            <i class="bi bi-check-circle me-2"></i>
            {{ __('app.finished_products.save_delivery_note') }}
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedBoxes = [];
    let currentBoxes = [];
    const allAvailableBoxes = @json($availableBoxes);

    // عرض بيانات التوصيل عند الإدخال
    function updateDeliveryInfoDisplay() {
        const driverName = $('#driverName').val().trim();
        const vehicleNumber = $('#vehicleNumber').val().trim();
        const cityName = $('#cityName').val().trim();
        
        const displayDiv = $('#deliveryInfoDisplay');
        const contentDiv = $('#deliveryInfoContent');
        
        if (driverName || vehicleNumber || cityName) {
            let html = '';
            
            if (driverName) {
                html += `
                    <div class="delivery-info-item">
                        <i class="bi bi-person-badge"></i>
                        <span class="delivery-info-label">{{ __('app.finished_products.driver_label') }}:</span>
                        <span class="delivery-info-value">${driverName}</span>
                    </div>
                `;
            }
            
            if (vehicleNumber) {
                html += `
                    <div class="delivery-info-item">
                        <i class="bi bi-card-text"></i>
                        <span class="delivery-info-label">{{ __('app.finished_products.vehicle_label') }}:</span>
                        <span class="delivery-info-value">${vehicleNumber}</span>
                    </div>
                `;
            }
            
            if (cityName) {
                html += `
                    <div class="delivery-info-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span class="delivery-info-label">{{ __('app.finished_products.destination_label') }}:</span>
                        <span class="delivery-info-value">${cityName}</span>
                    </div>
                `;
            }
            
            contentDiv.html(html);
            displayDiv.addClass('show');
        } else {
            displayDiv.removeClass('show');
        }
    }
    
    // تحديث العرض عند كتابة أي بيانات توصيل
    $('#driverName, #vehicleNumber, #cityName').on('input', updateDeliveryInfoDisplay);

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

    // تحديث قائمة الألوان حسب الصنف
    $('#filterMaterialType').on('change', function() {
        const selectedType = $(this).val();
        const colorsSet = new Set();

        allAvailableBoxes.forEach(box => {
            (box.materials || []).forEach(m => {
                if ((!selectedType || m.type === selectedType) && m.color && m.color !== 'غير محدد') {
                    colorsSet.add(m.color);
                }
            });
        });

        const $colorSelect = $('#filterColor');
        $colorSelect.empty();
        $colorSelect.append('<option value="">{{ __('app.buttons.all') }}</option>');
        Array.from(colorsSet).sort().forEach(color => {
            $colorSelect.append(`<option value="${color}">${color}</option>`);
        });
    });

    // إضافة صناديق حسب الصنف واللون والعدد
    function addBoxesByTypeColor() {
        const materialType = $('#filterMaterialType').val();
        const color = $('#filterColor').val();
        let quantity = parseInt($('#selectQuantity').val(), 10) || 0;

        if (!materialType && !color) {
            Swal.fire('تنبيه', 'اختر الصنف أو اللون أولاً', 'warning');
            return;
        }

        if (quantity <= 0) {
            Swal.fire('تنبيه', 'الرجاء إدخال عدد صحيح أكبر من صفر', 'warning');
            return;
        }

        const matching = allAvailableBoxes.filter(box => {
            if (selectedBoxes.some(b => b.id === box.id)) {
                return false;
            }

            const materials = box.materials || [];
            if (materials.length === 0) {
                return false;
            }

            return materials.some(m => {
                const typeOk = materialType ? m.type === materialType : true;
                const colorOk = color ? m.color === color : true;
                return typeOk && colorOk;
            });
        });

        if (matching.length === 0) {
            Swal.fire('معلومة', 'لا توجد صناديق مطابقة لهذه المواصفات حالياً', 'info');
            return;
        }

        const toAdd = matching.slice(0, quantity);
        toAdd.forEach(box => {
            selectedBoxes.push(box);
        });

        updateSummary();

        Swal.fire({
            icon: 'success',
            title: 'تم الإضافة',
            text: `تمت إضافة ${toAdd.length} صندوق/كراتين بناءً على الصنف واللون`,
            timer: 2000,
            showConfirmButton: false
        });
    }

    $('#addByTypeColor').on('click', function() {
        addBoxesByTypeColor();
    });

    $('#selectQuantity').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            addBoxesByTypeColor();
        }
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
                    <div class="selected-box-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary" style="font-size: 1rem; padding: 8px 12px;">${index + 1}</span>
                                    <strong style="font-size: 1.2rem; color: #27ae60;">${box.barcode}</strong>
                                </div>
                                <div class="d-flex gap-3 flex-wrap">
                                    <span class="badge bg-info" style="font-size: 0.9rem;">
                                        <i class="bi bi-box-seam me-1"></i>${box.packaging_type}
                                    </span>
                                    <span class="badge bg-success" style="font-size: 0.9rem;">
                                        <i class="bi bi-speedometer me-1"></i>${parseFloat(box.weight || 0).toFixed(2)} {{ __('app.units.kg') }}
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger remove-box" data-box-id="${box.id}" style="padding: 10px 16px;">
                                <i class="bi bi-trash me-1" style="font-size: 1.1em;"></i>
                                <span>حذف</span>
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
    $(window).on('scroll', function() {
        let submitBtn = $('#submitBtn');
        if (submitBtn.length > 0) {
            let submitBtnTop = submitBtn.offset().top;
            let scrollTop = $(window).scrollTop();
            let windowHeight = $(window).height();
            
            // إظهار الشريط عندما يكون زر الحفظ خارج الشاشة
            if (scrollTop + windowHeight < submitBtnTop || scrollTop > submitBtnTop + 200) {
                $('#stickyBar').addClass('show');
            } else {
                $('#stickyBar').removeClass('show');
            }
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
                        window.location.href = "{{ route('manufacturing.finished-product-deliveries.show', ':id') }}".replace(':id', response.delivery_note_id);
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