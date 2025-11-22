@extends('master')

@section('title', 'نقل البضاعة للإنتاج')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

<div class="container" style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
    <!-- Header Section -->
    <div style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); padding: 40px; border-radius: 12px; margin-bottom: 40px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0, 81, 229, 0.2);">
        <div>
            <h1 style="margin: 0; font-size: 32px; font-weight: 700; margin-bottom: 8px;">🏭 نقل البضاعة للإنتاج</h1>
            <p style="margin: 0; font-size: 16px; opacity: 0.9;">رقم الشحنة: <strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong></p>
        </div>
        <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" style="background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 2px solid white; transition: all 0.3s;">
            ← العودة
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        <!-- Main Form Card -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden;">
            <!-- Card Header -->
            <div style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); padding: 25px 30px; color: white; border-bottom: 4px solid #00a8ff;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 8px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 24px;">📦</div>
                    <div>
                        <h3 style="margin: 0; font-size: 20px; font-weight: 700;">بيانات النقل</h3>
                        <p style="margin: 4px 0 0 0; font-size: 13px; opacity: 0.9;">أدخل الكمية المراد نقلها للإنتاج</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div style="padding: 30px;">
                <form action="{{ route('manufacturing.warehouse.registration.transfer-to-production', $deliveryNote) }}" method="POST">
                    @csrf

                    <!-- Shipment Info Section -->
                    <div style="margin-bottom: 30px;">
                        <h5 style="color: #2c3e50; margin: 0 0 20px 0; font-weight: 700; font-size: 16px;">📋 معلومات الشحنة</h5>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; color: #7f8c8d; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">رقم الشحنة</label>
                                <div style="background: #f8f9fa; border: 2px solid #e9ecef; padding: 12px 16px; border-radius: 8px; font-weight: 600; color: #0051E5;">
                                    #{{ $deliveryNote->note_number ?? $deliveryNote->id }}
                                </div>
                            </div>
                            <div>
                                <label style="display: block; color: #7f8c8d; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">المورد</label>
                                <div style="background: #f8f9fa; border: 2px solid #e9ecef; padding: 12px 16px; border-radius: 8px; color: #2c3e50;">
                                    {{ $deliveryNote->supplier->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="display: block; color: #7f8c8d; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">المادة</label>
                            <div style="background: #f8f9fa; border: 2px solid #e9ecef; padding: 12px 16px; border-radius: 8px; color: #2c3e50;">
                                {{ $deliveryNote->material?->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Quantities Section -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #0051E5;">
                        <h5 style="color: #2c3e50; margin: 0 0 20px 0; font-weight: 700; font-size: 14px;">📊 تفاصيل الكميات</h5>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <!-- Registered Quantity -->
                            <div style="background: white; padding: 16px; border-radius: 8px; border-right: 4px solid #27ae60;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">📥 الكمية المسجلة</small>
                                <div style="font-size: 20px; font-weight: 700; color: #27ae60;">{{ number_format($registeredQuantity, 2) }} <span style="font-size: 14px; font-weight: 500;">كيلو</span></div>
                            </div>

                            <!-- Transferred Quantity -->
                            <div style="background: white; padding: 16px; border-radius: 8px; border-right: 4px solid #ff9800;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">🚚 المنقول بالفعل</small>
                                <div style="font-size: 20px; font-weight: 700; color: #ff9800;">{{ number_format($transferredQuantity, 2) }} <span style="font-size: 14px; font-weight: 500;">كيلو</span></div>
                            </div>

                            <!-- Available Quantity -->
                            <div style="background: white; padding: 16px; border-radius: 8px; border-right: 4px solid #3498db;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">✅ المتاح للنقل</small>
                                <div style="font-size: 20px; font-weight: 700; color: #3498db;">{{ number_format($availableQuantity, 2) }} <span style="font-size: 14px; font-weight: 500;">كيلو</span></div>
                            </div>

                            <!-- Warehouse Quantity -->
                            <div style="background: white; padding: 16px; border-radius: 8px; border-right: 4px solid #9c27b0;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">📦 في المستودع</small>
                                <div style="font-size: 20px; font-weight: 700; color: #9c27b0;">{{ number_format($warehouseQuantity, 2) }} <span style="font-size: 14px; font-weight: 500;">{{ $warehouseUnit }}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Input Section -->
                    <div style="background: #f0fdf4; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 2px solid #10b981;">
                        <h5 style="color: #10b981; margin: 0 0 20px 0; font-weight: 700; font-size: 14px;">✅ أدخل الكمية المراد نقلها</h5>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 10px;">الكمية (كيلو) <span style="color: #e74c3c; font-weight: 700;">*</span></label>
                            <div style="display: flex; gap: 10px;">
                                <input type="number"
                                       name="quantity"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       step="0.01"
                                       min="0.01"
                                       max="{{ $availableQuantity }}"
                                       value="{{ old('quantity', $availableQuantity) }}"
                                       placeholder="أدخل الكمية"
                                       required
                                       id="quantityInput"
                                       style="flex: 1; border: 2px solid #e9ecef; padding: 12px 16px; border-radius: 8px; font-size: 16px; font-weight: 600; color: #0051E5; transition: all 0.3s;">
                                <button type="button" class="btn btn-info" id="useFullBtn" style="background: #3498db; color: white; padding: 12px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; white-space: nowrap; transition: all 0.3s;">
                                    استخدم الكل
                                </button>
                            </div>
                            @error('quantity')
                                <div style="color: #e74c3c; font-size: 12px; margin-top: 8px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Preview -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div style="background: white; padding: 15px; border-radius: 8px; text-align: center;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">🔄 الحالة بعد النقل</small>
                                <div id="statusPreview" style="font-weight: 700; color: #3498db; font-size: 14px;">
                                    ← انتظر الإدخال
                                </div>
                            </div>
                            <div style="background: white; padding: 15px; border-radius: 8px; text-align: center;">
                                <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">📦 المتبقي</small>
                                <div id="remainingPreview" style="font-weight: 700; color: #27ae60; font-size: 14px;">
                                    ← انتظر الإدخال
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 10px;">ملاحظات (اختياري)</label>
                        <textarea name="notes"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  rows="3"
                                  placeholder="أدخل أي ملاحظات عن عملية النقل..."
                                  style="border: 2px solid #e9ecef; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div style="color: #e74c3c; font-size: 12px; margin-top: 8px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                        <button type="submit" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; padding: 14px 24px; border-radius: 8px; border: none; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(39, 174, 96, 0.2);">
                            ✅ تأكيد النقل
                        </button>
                        <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" style="background: #95a5a6; color: white; padding: 14px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 15px; text-align: center; transition: all 0.3s;">
                            ❌ إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden;">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 25px 30px; border-bottom: 2px solid #90caf9;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-size: 28px;">ℹ️</div>
                    <h3 style="margin: 0; color: #0051E5; font-size: 18px; font-weight: 700;">معلومات مهمة</h3>
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <div style="background: #f0f4f8; padding: 20px; border-radius: 10px; border-right: 4px solid #0051E5;">
                    <h6 style="color: #0051E5; margin: 0 0 16px 0; font-weight: 700; font-size: 14px;">🎯 فهم آلية النقل:</h6>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- Item 1 -->
                        <div style="padding: 12px; background: white; border-radius: 8px; border-right: 3px solid #ff9800;">
                            <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 4px;">💰 النقل الجزئي</small>
                            <small style="color: #555; line-height: 1.5;">عند نقل كمية أقل من المتاح، تبقى الشحنة "مسجلة" ويمكنك نقل المتبقي لاحقاً</small>
                        </div>

                        <!-- Item 2 -->
                        <div style="padding: 12px; background: white; border-radius: 8px; border-right: 3px solid #27ae60;">
                            <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 4px;">✅ النقل الكامل</small>
                            <small style="color: #555; line-height: 1.5;">عند نقل الكمية الكاملة، تنتقل الشحنة إلى حالة "في الإنتاج"</small>
                        </div>

                        <!-- Item 3 -->
                        <div style="padding: 12px; background: white; border-radius: 8px; border-right: 3px solid #3498db;">
                            <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 4px;">📋 سجل الحركات</small>
                            <small style="color: #555; line-height: 1.5;">جميع عمليات النقل تُسجل تلقائياً في سجل الحركات</small>
                        </div>

                        <!-- Item 4 -->
                        <div style="padding: 12px; background: white; border-radius: 8px; border-right: 3px solid #9c27b0;">
                            <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 4px;">🔢 الدفعات</small>
                            <small style="color: #555; line-height: 1.5;">كل شحنة تحصل على رقم دفعة فريد للتتبع</small>
                        </div>

                        <!-- Item 5 -->
                        <div style="padding: 12px; background: white; border-radius: 8px; border-right: 3px solid #e74c3c;">
                            <small style="display: block; color: #7f8c8d; font-weight: 600; margin-bottom: 4px;">📦 خصم المستودع</small>
                            <small style="color: #555; line-height: 1.5;">الكميات المنقولة تُخصم تلقائياً من المستودع</small>
                        </div>
                    </div>
                </div>

                <!-- Warning Box -->
                <div style="background: #fff3cd; padding: 16px; border-radius: 8px; margin-top: 20px; border-right: 3px solid #ffc107;">
                    <small style="color: #856404; line-height: 1.6; display: block;">
                        <strong>⚠️ تنبيه:</strong> إذا تجاوزت الكمية المتاحة، سيتم عرض تحذير لكن النقل سيتم بشكل آمن
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #0051E5;
        box-shadow: 0 0 0 4px rgba(0, 81, 229, 0.1);
        outline: none;
    }

    .btn-info {
        transition: all 0.3s;
    }

    .btn-info:hover {
        background: #2980b9 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            display: grid !important;
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    const quantityInput = document.getElementById('quantityInput');
    const useFullBtn = document.getElementById('useFullBtn');
    const statusPreview = document.getElementById('statusPreview');
    const remainingPreview = document.getElementById('remainingPreview');

    const availableQuantity = {{ $availableQuantity }};
    const registeredQuantity = {{ $registeredQuantity }};

    // استخدم الكل
    useFullBtn.addEventListener('click', function() {
        quantityInput.value = {{ $availableQuantity }};
        quantityInput.dispatchEvent(new Event('input'));
    });

    // تحديث المعاينة
    quantityInput.addEventListener('input', function() {
        const entered = parseFloat(this.value) || 0;
        const remaining = availableQuantity - entered;

        if (entered > 0) {
            const isFullTransfer = Math.abs(entered - availableQuantity) < 0.01;

            if (isFullTransfer) {
                statusPreview.innerHTML = '<span style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 6px; display: inline-block;">🏭 في الإنتاج (نقل كامل)</span>';
                statusPreview.style.color = '#27ae60';
            } else {
                statusPreview.innerHTML = '<span style="background: #3498db; color: white; padding: 6px 12px; border-radius: 6px; display: inline-block;">📋 مسجلة (نقل جزئي)</span>';
                statusPreview.style.color = '#3498db';
            }

            remainingPreview.textContent = remaining.toFixed(2) + ' كيلو';
            remainingPreview.style.color = remaining > 0 ? '#ff9800' : '#27ae60';
        } else {
            statusPreview.innerHTML = '← انتظر الإدخال';
            statusPreview.style.color = '#3498db';
            remainingPreview.textContent = '← انتظر الإدخال';
            remainingPreview.style.color = '#3498db';
        }
    });
</script>
@endsection
