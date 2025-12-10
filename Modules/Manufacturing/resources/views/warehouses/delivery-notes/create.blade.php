@extends('master')

@section('title', __('delivery_notes.new_delivery_note'))

@section('content')
<!-- مكتبة JsBarcode لتوليد باركود قابل للمسح -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<style>
    .simple-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .step-indicator {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .step-number {
        background: rgba(255,255,255,0.3);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        border: 3px solid white;
    }

    .simple-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 20px;
        font-weight: bold;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group-simple {
        margin-bottom: 25px;
    }

    .label-simple {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .required-mark {
        color: #e74c3c;
        font-size: 18px;
    }

    .input-simple {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .input-simple:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .input-simple:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .type-selector {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }

    .type-option {
        padding: 20px;
        border: 3px solid #e0e0e0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        position: relative;
    }

    .type-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .type-option input[type="radio"]:checked + .type-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .type-content {
        padding: 15px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .type-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .type-text {
        font-size: 18px;
        font-weight: bold;
    }

    .btn-submit-simple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit-simple:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-cancel-simple {
        background: #95a5a6;
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        text-align: center;
        display: block;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-cancel-simple:hover {
        background: #7f8c8d;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .helper-text {
        background: #e8f5e9;
        border-right: 4px solid #4caf50;
        padding: 12px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 14px;
        color: #2e7d32;
    }

    .alert-simple {
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 16px;
    }

    .alert-success {
        background: #d4edda;
        border: 2px solid #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background: #f8d7da;
        border: 2px solid #f5c6cb;
        color: #721c24;
    }
</style>

<div class="simple-container">
    <div class="step-indicator">
        <div class="step-number">1</div>
        <div style="flex: 1;">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">📋 {{ __('delivery_notes.create_delivery_note') }}</div>
            <div style="opacity: 0.9;">{{ __('delivery_notes.next_step_info') }}</div>
        </div>
        <div style="opacity: 0.5; display: flex; align-items: center; gap: 10px;">
            <span>→</span>
            <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px dashed white; display: flex; align-items: center; justify-content: center;">2</div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-simple alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-simple alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-simple alert-error">
            <strong>{{ __('delivery_notes.data_error') }}:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacturing.delivery-notes.store') }}" id="deliveryForm">
        @csrf

        @if (!auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_CREATE'))
            <div class="alert-simple alert-error" style="margin-bottom: 20px;">
                ❌ {{ __('delivery_notes.no_permission_to_create') }}
            </div>
            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn-cancel-simple">
                ← {{ __('delivery_notes.back') }}
            </a>
        @else
        <div class="simple-card">
            <div class="card-title">
                🔄 {{ __('delivery_notes.type') }}
            </div>

            <div class="type-selector">
                <label class="type-option">
                    <input type="radio" name="type" value="incoming" checked>
                    <div class="type-content">
                        <div class="type-icon">📥</div>
                        <div class="type-text">{{ __('delivery_notes.incoming') }}</div>
                        <small>{{ __('delivery_notes.from_supplier') }}</small>
                    </div>
                </label>

                <label class="type-option">
                    <input type="radio" name="type" value="outgoing">
                    <div class="type-content">
                        <div class="type-icon">📤</div>
                        <div class="type-text">{{ __('delivery_notes.outgoing') }}</div>
                        <small>{{ __('delivery_notes.to_outside') }}</small>
                    </div>
                </label>
            </div>
        </div>

        <input type="hidden" name="delivery_date" value="{{ date('Y-m-d') }}">

        <div class="simple-card" id="incomingCard">
            <div class="card-title">
                📥 {{ __('delivery_notes.incoming_shipment_data') }}
            </div>

            <div class="form-group-simple">
                <label class="label-simple">🏭 {{ __('delivery_notes.warehouse') }} <span class="required-mark">*</span></label>
                <select name="warehouse_id" id="warehouseSelect" class="input-simple" required>
                    <option value="">{{ __('delivery_notes.select_warehouse') }}</option>
                    @foreach($warehouses ?? [] as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-simple">
                <label class="label-simple">🎲 {{ __('delivery_notes.coil_number') }} ({{ __('delivery_notes.optional') }})</label>
                <input type="text" name="coil_number" class="input-simple" placeholder="{{ __('delivery_notes.enter_coil_number_if_exists') }}">
                <div class="helper-text">
                    ✓ {{ __('delivery_notes.can_enter_coil_for_tracking') }}
                </div>
            </div>

            <div class="form-group-simple">
                <label class="label-simple">📦 {{ __('delivery_notes.material') }} <span class="required-mark">*</span></label>
                <select name="material_id" id="materialSelect" class="input-simple" required>
                    <option value="">{{ __('delivery_notes.select_material') }}</option>
                    @foreach($materials ?? [] as $material)
                        <option value="{{ $material->id }}">{{ $material->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-simple" id="quantityField">
                <label class="label-simple">⚖️ {{ __('delivery_notes.quantity') }} <span class="required-mark" id="quantityRequired">*</span></label>
                <input type="number" name="quantity" id="totalQuantity" class="input-simple" placeholder="{{ __('delivery_notes.enter_quantity_placeholder') }}" step="0.01" min="0.01" required>
                <div class="helper-text">
                    ✓ {{ __('delivery_notes.will_be_registered_automatically') }}
                </div>
            </div>

            <!-- قسم الكويلات -->
            <div class="form-group-simple">
                <label class="label-simple" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="hasCoilsCheckbox" name="has_coils" value="1" style="width: auto; height: 20px;">
                    <span>🎲 هل الشحنة تحتوي على كويلات متعددة؟</span>
                </label>
                <input type="hidden" id="hasCoilsData" name="has_coils_data" value="0">
                <div class="helper-text">
                    ✓ فعّل هذا الخيار إذا كانت الشحنة مقسمة إلى عدة كويلات بأوزان مختلفة (سيتم حساب الكمية الإجمالية تلقائياً)
                </div>
            </div>

            <div id="coilsSection" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px; border: 2px dashed #667eea;">
                <h4 style="color: #667eea; margin-bottom: 20px;">📦 إدارة الكويلات</h4>
                
                <!-- زر إضافة كويل جديد -->
                <div id="addCoilBtnContainer" style="margin-bottom: 20px;">
                    <button type="button" id="showCoilFormBtn" class="btn-submit" style="background: #4caf50; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        ➕ إضافة كويل جديد
                    </button>
                </div>
                
                <!-- نموذج إضافة كويل واحد (مخفي افتراضياً) -->
                <div id="coilFormContainer" style="display: none; background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ddd;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 120px 120px; gap: 15px; align-items: end;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">🔢 رقم الكويل</label>
                            <input type="text" id="newCoilNumber" class="input-simple" placeholder="اتركه فارغاً للتوليد التلقائي">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">⚖️ الوزن (كجم) <span style="color: red;">*</span></label>
                            <input type="number" id="newCoilWeight" class="input-simple" placeholder="أدخل الوزن" step="0.001" min="0.001">
                        </div>
                        <div>
                            <button type="button" id="addCoilBtn" onclick="return false;" class="btn-submit" style="width: 100%; margin: 0; padding: 10px; background: #4caf50;">
                                ✓ حفظ
                            </button>
                        </div>
                        <div>
                            <button type="button" id="cancelCoilBtn" class="btn-submit" style="width: 100%; margin: 0; padding: 10px; background: #95a5a6;">
                                ✕ إلغاء
                            </button>
                        </div>
                    </div>
                    <div style="margin-top: 10px; padding: 10px; background: #e3f2fd; border-radius: 5px;">
                        <small style="color: #1565c0;">
                            💡 <strong>نصيحة:</strong> اضغط Enter بعد إدخال الوزن للحفظ السريع، سيتم توليد الباركود فوراً
                        </small>
                    </div>
                </div>

                <!-- قائمة الكويلات المضافة -->
                <div id="coilsListContainer" style="margin-top: 20px;">
                    <h5 style="color: #555; margin-bottom: 15px;">الكويلات المضافة:</h5>
                    <div id="coilsList"></div>
                </div>
                
                <div style="margin-top: 15px; padding: 15px; background: #e8f5e9; border-radius: 8px;" id="coilsSummary">
                    <strong>📊 ملخص الكويلات:</strong>
                    <div style="margin-top: 10px;">
                        <div>عدد الكويلات: <span id="summaryCoilCount">0</span></div>
                        <div style="font-size: 18px; font-weight: bold; color: #2e7d32;">إجمالي الوزن: <span id="summaryTotalWeight">0</span> كجم</div>
                    </div>
                </div>

                <input type="hidden" id="totalCoils" name="total_coils" value="0">
            </div>

            <!-- حقول إضافية -->
            <div class="form-group-simple">
                <label class="label-simple">🚗 رقم لوحة السيارة (اختياري)</label>
                <input type="text" name="vehicle_plate_number" class="input-simple" placeholder="مثال: ABC-1234">
            </div>

            <div class="form-group-simple">
                <label class="label-simple">👤 اسم الشخص المستلم منه (اختياري)</label>
                <input type="text" name="received_from_person" class="input-simple" placeholder="أدخل اسم الشخص">
            </div>
        </div>

        <div class="simple-card" id="outgoingCard" style="display: none;">
            <div class="card-title">
                📤 {{ __('delivery_notes.outgoing_shipment_data') }}
            </div>

            <div class="form-group-simple">
                <label class="label-simple">🏢 {{ __('delivery_notes.source_warehouse') }} <span class="required-mark">*</span></label>
                <select name="warehouse_from_id" id="warehouseFromSelect" class="input-simple">
                    <option value="">{{ __('delivery_notes.select_warehouse') }}</option>
                    @foreach($warehouses ?? [] as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-simple">
                <label class="label-simple">📦 {{ __('delivery_notes.material') }} <span class="required-mark">*</span></label>
                <select name="material_detail_id" id="materialDetailSelect" class="input-simple">
                    <option value="">{{ __('delivery_notes.select_material') }}</option>
                </select>
            </div>

            <div class="form-group-simple">
                <label class="label-simple">⚖️ {{ __('delivery_notes.quantity') }} <span class="required-mark">*</span></label>
                <input type="number" name="delivery_quantity" class="input-simple" placeholder="{{ __('delivery_notes.enter_quantity_placeholder') }}" step="0.01" min="0.01">
            </div>

            <div class="form-group-simple">
                <label class="label-simple">🎯 {{ __('delivery_notes.destination') }} <span class="required-mark">*</span></label>
                <select name="destination_id" class="input-simple">
                    <option value="">{{ __('delivery_notes.select_destination') }}</option>
                    <option value="client">👤 {{ __('delivery_notes.to_client') }}</option>
                    <option value="production_transfer">🚚 {{ __('delivery_notes.production_transfer') }}</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit-simple">
                <span style="font-size: 24px;">✓</span>
                <span>{{ __('delivery_notes.save_and_next') }}</span>
            </button>
            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn-cancel-simple">
                ✕ {{ __('delivery_notes.cancel') }}
            </a>
        </div>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const incomingCard = document.getElementById('incomingCard');
    const outgoingCard = document.getElementById('outgoingCard');
    const hasCoilsCheckbox = document.getElementById('hasCoilsCheckbox');
    const coilsSection = document.getElementById('coilsSection');
    const totalQuantityInput = document.getElementById('totalQuantity');
    const newCoilNumberInput = document.getElementById('newCoilNumber');
    const newCoilWeightInput = document.getElementById('newCoilWeight');
    const addCoilBtn = document.getElementById('addCoilBtn');
    const coilsList = document.getElementById('coilsList');
    const totalCoilsInput = document.getElementById('totalCoils');
    const warehouseSelect = document.getElementById('warehouseSelect');
    const materialSelect = document.getElementById('materialSelect');
    const showCoilFormBtn = document.getElementById('showCoilFormBtn');
    const coilFormContainer = document.getElementById('coilFormContainer');
    const cancelCoilBtn = document.getElementById('cancelCoilBtn');
    const addCoilBtnContainer = document.getElementById('addCoilBtnContainer');

    let coilsData = []; // مصفوفة لتخزين بيانات الكويلات
    
    // إظهار نموذج إضافة كويل
    showCoilFormBtn.addEventListener('click', function() {
        coilFormContainer.style.display = 'block';
        addCoilBtnContainer.style.display = 'none';
        newCoilNumberInput.focus();
    });
    
    // إلغاء وإخفاء نموذج الكويل
    cancelCoilBtn.addEventListener('click', function() {
        coilFormContainer.style.display = 'none';
        addCoilBtnContainer.style.display = 'block';
        newCoilNumberInput.value = '';
        newCoilWeightInput.value = '';
    });

    // Toggle between incoming and outgoing
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'incoming') {
                incomingCard.style.display = 'block';
                outgoingCard.style.display = 'none';
                document.querySelector('[name="warehouse_id"]').required = true;
                document.querySelector('[name="material_id"]').required = true;
                document.querySelector('[name="quantity"]').required = true;
                document.querySelector('[name="warehouse_from_id"]').required = false;
                document.querySelector('[name="material_detail_id"]').required = false;
                document.querySelector('[name="delivery_quantity"]').required = false;
                document.querySelector('[name="destination_id"]').required = false;
            } else {
                incomingCard.style.display = 'none';
                outgoingCard.style.display = 'block';
                document.querySelector('[name="warehouse_id"]').required = false;
                document.querySelector('[name="material_id"]').required = false;
                document.querySelector('[name="quantity"]').required = false;
                document.querySelector('[name="warehouse_from_id"]').required = true;
                document.querySelector('[name="material_detail_id"]').required = true;
                document.querySelector('[name="delivery_quantity"]').required = true;
                document.querySelector('[name="destination_id"]').required = true;
            }
        });
    });

    // Toggle coils section
    hasCoilsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            coilsSection.style.display = 'block';
            totalQuantityInput.readOnly = true;
            totalQuantityInput.required = false;
            totalQuantityInput.style.backgroundColor = '#f0f0f0';
            totalQuantityInput.placeholder = 'سيتم الحساب تلقائياً من الكويلات';
            document.getElementById('quantityRequired').style.display = 'none';
            document.getElementById('hasCoilsData').value = '1';
        } else {
            coilsSection.style.display = 'none';
            coilsData = [];
            renderCoilsList();
            totalQuantityInput.readOnly = false;
            totalQuantityInput.required = true;
            totalQuantityInput.style.backgroundColor = '';
            totalQuantityInput.placeholder = '{{ __('delivery_notes.enter_quantity_placeholder') }}';
            document.getElementById('quantityRequired').style.display = 'inline';
            document.getElementById('hasCoilsData').value = '0';
        }
    });

    // إضافة كويل جديد عبر AJAX
    addCoilBtn.addEventListener('click', function() {
        const weight = parseFloat(newCoilWeightInput.value);
        const warehouseId = warehouseSelect.value;
        const materialId = materialSelect.value;
        
        if (!warehouseId) {
            alert('⚠️ يرجى اختيار المستودع أولاً');
            warehouseSelect.focus();
            return;
        }
        
        if (!materialId) {
            alert('⚠️ يرجى اختيار المادة أولاً');
            materialSelect.focus();
            return;
        }
        
        if (!weight || weight <= 0) {
            alert('⚠️ يرجى إدخال وزن صحيح للكويل');
            newCoilWeightInput.focus();
            return;
        }

        // تعطيل الزر أثناء الحفظ
        addCoilBtn.disabled = true;
        addCoilBtn.innerHTML = '⏳ جاري الحفظ...';

        // إرسال AJAX لحفظ الكويل وتوليد الباركود
        fetch('{{ route("manufacturing.delivery-notes.add-coil-temp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                warehouse_id: warehouseId,
                material_id: materialId,
                coil_number: newCoilNumberInput.value.trim() || null,
                coil_weight: weight
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إضافة الكويل للقائمة المحلية
                coilsData.push(data.coil);
                
                // مسح الحقول وإخفاء النموذج
                newCoilNumberInput.value = '';
                newCoilWeightInput.value = '';
                coilFormContainer.style.display = 'none';
                addCoilBtnContainer.style.display = 'block';

                // تحديث القائمة
                renderCoilsList();
                updateSummary();
                
                // إظهار رسالة نجاح
                showSuccessMessage('✅ تم إضافة الكويل بنجاح وتوليد الباركود');
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ حدث خطأ أثناء حفظ الكويل');
        })
        .finally(() => {
            // إعادة تفعيل الزر
            addCoilBtn.disabled = false;
            addCoilBtn.innerHTML = '➕ إضافة';
        });
    });

    // رسالة نجاح مؤقتة
    function showSuccessMessage(message) {
        const msgDiv = document.createElement('div');
        msgDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s;';
        msgDiv.textContent = message;
        document.body.appendChild(msgDiv);
        setTimeout(() => {
            msgDiv.style.animation = 'slideOut 0.3s';
            setTimeout(() => msgDiv.remove(), 300);
        }, 3000);
    }

    // Enter key للإضافة السريعة
    newCoilWeightInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addCoilBtn.click();
        }
    });
    
    newCoilNumberInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            newCoilWeightInput.focus(); // الانتقال إلى حقل الوزن
        }
    });

    // عرض قائمة الكويلات
    function renderCoilsList() {
        if (coilsData.length === 0) {
            coilsList.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">لم يتم إضافة أي كويلات بعد</p>';
            return;
        }

        let html = '';
        coilsData.forEach((coil, index) => {
            html += `
                <div class="coil-item" id="coil-${coil.id}" style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ddd; display: grid; grid-template-columns: 40px 1fr 1fr 2fr 70px 70px 70px; gap: 10px; align-items: center;">
                    <div style="font-weight: bold; color: #667eea;">#${index + 1}</div>
                    <div>
                        <small style="color: #777;">رقم الكويل</small>
                        <div style="font-weight: 600;" class="coil-number-display">${coil.coil_number}</div>
                    </div>
                    <div>
                        <small style="color: #777;">الوزن</small>
                        <div style="font-weight: 600; color: #2e7d32;" class="coil-weight-display">${parseFloat(coil.coil_weight).toFixed(3)} كجم</div>
                    </div>
                    <div>
                        <small style="color: #777;">الباركود</small>
                        <svg class="coil-barcode-svg" data-barcode="${coil.coil_barcode}" style="max-width: 100%;"></svg>
                        <div style="font-size: 9px; font-family: monospace; color: #555; text-align: center;">${coil.coil_barcode}</div>
                    </div>
                    <button type="button" onclick="editCoil('${coil.id}')" class="btn-edit" style="background: #2196f3; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer;" title="تعديل">
                        ✏️
                    </button>
                    <button type="button" onclick="printCoilBarcode('${coil.coil_number}', ${coil.coil_weight}, '${coil.coil_barcode}')" class="btn-print" style="background: #4caf50; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer;" title="طباعة">
                        🖨️
                    </button>
                    <button type="button" onclick="removeCoil('${coil.id}')" class="btn-remove" style="background: #f44336; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer;" title="حذف">
                        🗑️
                    </button>
                </div>
            `;
        });

        coilsList.innerHTML = html;
        
        // توليد الباركودات
        setTimeout(function() {
            document.querySelectorAll('.coil-barcode-svg').forEach(function(svg) {
                const code = svg.getAttribute('data-barcode');
                try {
                    JsBarcode(svg, code, {
                        format: "CODE128",
                        width: 1.5,
                        height: 40,
                        displayValue: false,
                        margin: 2
                    });
                } catch (e) {
                    console.error('Error generating barcode:', e);
                }
            });
        }, 100);
    }

    // تعديل كويل
    window.editCoil = function(coilId) {
        const coil = coilsData.find(c => c.id === coilId);
        if (!coil) return;

        const newNumber = prompt('تعديل رقم الكويل:', coil.coil_number);
        if (newNumber === null) return; // ألغى المستخدم

        const newWeight = prompt('تعديل الوزن (كجم):', coil.coil_weight);
        if (newWeight === null) return; // ألغى المستخدم

        const weight = parseFloat(newWeight);
        if (isNaN(weight) || weight <= 0) {
            alert('⚠️ يرجى إدخال وزن صحيح');
            return;
        }

        // إرسال AJAX لتحديث الكويل في الجلسة
        fetch('{{ route("manufacturing.delivery-notes.update-coil-temp") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                coil_id: coilId,
                coil_number: newNumber.trim(),
                coil_weight: weight
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // تحديث البيانات المحلية
                coil.coil_number = newNumber.trim();
                coil.coil_weight = weight;
                
                renderCoilsList();
                updateSummary();
                
                showSuccessMessage('✅ تم تعديل الكويل بنجاح');
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ حدث خطأ أثناء تعديل الكويل');
        });
    };

    // حذف كويل
    window.removeCoil = function(coilId) {
        if (!confirm('هل أنت متأكد من حذف هذا الكويل؟')) return;

        // إرسال AJAX لحذف الكويل من الجلسة
        fetch('{{ route("manufacturing.delivery-notes.delete-coil-temp") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                coil_id: coilId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // حذف من البيانات المحلية
                coilsData = coilsData.filter(c => c.id !== coilId);
                renderCoilsList();
                updateSummary();
                
                showSuccessMessage('✅ تم حذف الكويل بنجاح');
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ حدث خطأ أثناء حذف الكويل');
        });
    };

    // دالة لإظهار رسالة نجاح
    function showSuccessMessage(message) {
        const msgDiv = document.createElement('div');
        msgDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; animation: slideIn 0.3s ease-out;';
        msgDiv.textContent = message;
        document.body.appendChild(msgDiv);
        
        setTimeout(() => {
            msgDiv.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => msgDiv.remove(), 300);
        }, 3000);
    }

    // طباعة باركود كويل
    window.printCoilBarcode = function(coilNumber, weight, barcode) {
        const printWindow = window.open('', '_blank', 'width=400,height=300');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>طباعة باركود - ${coilNumber}</title>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .barcode-container {
                        text-align: center;
                        border: 2px solid #000;
                        padding: 20px;
                        background: white;
                    }
                    .info {
                        margin: 10px 0;
                        font-size: 14px;
                    }
                    @media print {
                        body { padding: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="barcode-container">
                    <h3>🏭 مصنع الحديد</h3>
                    <div class="info"><strong>رقم الكويل:</strong> ${coilNumber}</div>
                    <div class="info"><strong>الوزن:</strong> ${weight.toFixed(3)} كجم</div>
                    <svg id="printBarcode"></svg>
                    <div class="info" style="font-size: 11px; color: #666;">تاريخ الطباعة: ${new Date().toLocaleString('ar-EG')}</div>
                </div>
                <script>
                    JsBarcode("#printBarcode", "${barcode}", {
                        format: "CODE128",
                        width: 2,
                        height: 80,
                        displayValue: true,
                        fontSize: 14,
                        margin: 10
                    });
                    window.onload = function() {
                        window.print();
                        setTimeout(function() { window.close(); }, 100);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    };

    // تحديث الملخص
    function updateSummary() {
        const totalCoils = coilsData.length;
        const totalWeight = coilsData.reduce((sum, coil) => sum + parseFloat(coil.coil_weight), 0);

        document.getElementById('summaryCoilCount').textContent = totalCoils;
        document.getElementById('summaryTotalWeight').textContent = totalWeight.toFixed(3);
        totalCoilsInput.value = totalCoils;

        // تحديث حقل الكمية الإجمالي
        if (hasCoilsCheckbox.checked) {
            totalQuantityInput.value = totalWeight.toFixed(3);
        }
        
        // تحديث حقل has_coils_data
        if (totalCoils > 0) {
            document.getElementById('hasCoilsData').value = '1';
        }
    }

    const warehouseFromSelect = document.getElementById('warehouseFromSelect');
    const materialDetailSelect = document.getElementById('materialDetailSelect');

    warehouseFromSelect.addEventListener('change', function() {
        const warehouseId = this.value;
        materialDetailSelect.innerHTML = '<option value="">{{ __('delivery_notes.loading') }}...</option>';

        if (warehouseId) {
            fetch(`/manufacturing/warehouses/${warehouseId}/materials`)
                .then(response => response.json())
                .then(data => {
                    materialDetailSelect.innerHTML = '<option value="">{{ __('delivery_notes.select_material') }}</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = `${item.material_name} ({{ __('delivery_notes.available') }}: ${item.quantity} ${item.unit_name})`;
                        materialDetailSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    materialDetailSelect.innerHTML = '<option value="">{{ __('delivery_notes.error_loading') }}</option>';
                });
        } else {
            materialDetailSelect.innerHTML = '<option value="">{{ __('delivery_notes.select_material') }}</option>';
        }
    });
});
</script>

@endsection