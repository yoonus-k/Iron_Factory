@extends('master')

@section('title', 'نقل البضاعة للإنتاج')

@section('content')

        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="btn btn-link">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-arrow-right"></i> نقل البضاعة للإنتاج
                    </h1>
                    <p class="text-muted mb-0">اختر الكمية المراد نقلها وقم بالنقل</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ خطأ!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <!-- معلومات البضاعة -->
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
                        <h3 class="card-title mb-0" style="color: white;">
                            <i class="fas fa-info-circle"></i> معلومات البضاعة
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">رقم الأذن:</label>
                                    <div class="info-value">{{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">المادة:</label>
                                    <div class="info-value">{{ $deliveryNote->material?->name ?? 'غير محددة' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">المورد:</label>
                                    <div class="info-value">{{ $deliveryNote->supplier?->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">تاريخ الاستقبال:</label>
                                    <div class="info-value">{{ $deliveryNote->delivery_date?->format('Y-m-d H:i') ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fas fa-box"></i>
                            <strong>حالة الكميات:</strong>
                            <div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 3px;">الكمية في المستودع:</div>
                                    <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                        {{ number_format($availableQuantity, 2) }} كيلو
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 3px;">الكمية المتاحة للنقل:</div>
                                    <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                                        {{ number_format($availableQuantity, 2) }} كيلو
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- شريط التقدم -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="font-weight: 600; color: #2c3e50; margin-bottom: 0;">الكمية الكلية:</label>
                                <span style="font-weight: 600; color: #3498db;">
                                    {{ number_format($availableQuantity, 2) }} كيلو
                                </span>
                            </div>
                            <div class="progress" style="height: 25px; border-radius: 4px; overflow: hidden;">
                                <div class="progress-bar"
                                    style="width: 100%; background: linear-gradient(90deg, #3498db 0%, #2980b9 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                    {{ number_format($availableQuantity, 2) }} كيلو
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نموذج النقل -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">
                        <h3 class="card-title mb-0" style="color: white;">
                            <i class="fas fa-arrow-right"></i> نقل البضاعة
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manufacturing.warehouse.registration.transfer-to-production', $deliveryNote) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="quantity" class="form-label" style="font-weight: 600;">
                                    <i class="fas fa-weight"></i> الكمية المراد نقلها (كيلو):
                                    <span style="color: #e74c3c;">*</span>
                                </label>

                                <div style="position: relative;">
                                    <input
                                        type="number"
                                        id="quantity"
                                        name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        step="0.01"
                                        min="0.01"
                                        max="{{ $availableQuantity }}"
                                        placeholder="أدخل الكمية"
                                        required
                                        style="padding: 12px; font-size: 16px; border-radius: 4px; border: 2px solid #ddd; transition: all 0.3s;">
                                </div>

                                <!-- رسالة المساعدة -->
                                <small class="form-text" style="color: #666; margin-top: 8px;">
                                    <i class="fas fa-info-circle"></i>
                                    الحد الأقصى: <strong>{{ number_format($availableQuantity, 2) }}</strong> كيلو
                                </small>

                                @error('quantity')
                                    <div class="invalid-feedback" style="display: block; margin-top: 5px;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- شريط معلومات الكمية -->
                            <div class="alert alert-warning" style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-lightbulb" style="font-size: 18px;"></i>
                                    <div>
                                        <strong>ملاحظة:</strong> الكمية المدخلة ستُخصم من المستودع وتُضاف للإنتاج
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label" style="font-weight: 600;">
                                    <i class="fas fa-sticky-note"></i> ملاحظات (اختياري):
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="مثال: نقل جزئي بسبب الصيانة"
                                    style="padding: 12px; border-radius: 4px; border: 1px solid #ddd; font-size: 14px;"></textarea>

                                <small class="form-text" style="color: #666; margin-top: 5px;">
                                    أضف أي ملاحظات متعلقة بعملية النقل
                                </small>
                            </div>

                            <!-- معاينة -->
                            <div class="alert alert-info">
                                <strong>معاينة:</strong>
                                <div style="margin-top: 10px; font-size: 13px; line-height: 1.8;">
                                    <div>
                                        <i class="fas fa-arrow-down" style="color: #3498db;"></i>
                                        <strong>قبل النقل:</strong>
                                    </div>
                                    <div style="margin-right: 20px; margin-bottom: 10px;">
                                        المستودع: <strong id="preview-warehouse">{{ number_format($availableQuantity, 2) }}</strong> كيلو
                                    </div>

                                    <div>
                                        <i class="fas fa-arrow-up" style="color: #27ae60;"></i>
                                        <strong>بعد النقل:</strong>
                                    </div>
                                    <div style="margin-right: 20px;">
                                        المستودع: <strong id="preview-warehouse-after">{{ number_format($availableQuantity, 2) }}</strong> كيلو
                                    </div>
                                </div>
                            </div>

                            <!-- الأزرار -->
                            <div style="display: flex; gap: 10px; justify-content: space-between;">
                                <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="btn btn-outline-secondary" style="flex: 1; border: 2px solid #bdc3c7; color: #2c3e50;">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-success" style="flex: 1; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); border: none; font-weight: 600;">
                                    <i class="fas fa-check"></i> تأكيد النقل
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    <style>
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-link {
            color: #3498db;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-label {
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            border: none;
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #bdc3c7;
            color: #2c3e50;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: #ecf0f1;
            border-color: #95a5a6;
        }

        .progress {
            background-color: #ecf0f1;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 4px;
            border: none;
        }

        #quantity {
            font-size: 16px;
        }

        #quantity:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>

    <script>
        const quantityInput = document.getElementById('quantity');
        const previewWarehouse = document.getElementById('preview-warehouse');
        const previewWarehouseAfter = document.getElementById('preview-warehouse-after');
        const availableQuantity = {{ $availableQuantity }};

        quantityInput.addEventListener('input', function() {
            const quantity = parseFloat(this.value) || 0;
            const remaining = Math.max(0, availableQuantity - quantity);

            previewWarehouseAfter.textContent = remaining.toFixed(2);

            // تحديث اللون بناءً على الكمية
            if (quantity > availableQuantity) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // التحقق من الصيغة عند الإرسال
        document.querySelector('form').addEventListener('submit', function(e) {
            const quantity = parseFloat(quantityInput.value);
            if (quantity > availableQuantity) {
                e.preventDefault();
                alert('الكمية المدخلة تتجاوز الكمية المتاحة!');
            }
        });
    </script>
@endsection
