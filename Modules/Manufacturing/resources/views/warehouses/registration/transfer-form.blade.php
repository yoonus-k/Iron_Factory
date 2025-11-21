@extends('master')

@section('title', 'نقل البضاعة للإنتاج')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

<div class="container">
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="course-icon">
                    <i class="feather icon-truck"></i>
                </div>
                <div class="header-info">
                    <h1>🏭 نقل البضاعة للإنتاج</h1>
                    <p class="text-muted">رقم الشحنة: #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="btn btn-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <div class="grid">
        <!-- نموذج النقل -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
                <h3 class="card-title">📦 بيانات النقل للإنتاج</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('manufacturing.warehouse.registration.transfer-to-production', $deliveryNote) }}" method="POST">
                    @csrf

                    <!-- معلومات الشحنة -->
                    <div class="form-section" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h5 style="color: #2c3e50; margin-bottom: 15px; font-weight: 600;">
                            📋 معلومات الشحنة
                        </h5>

                        <div class="form-group">
                            <label class="form-label">رقم الشحنة:</label>
                            <div class="form-control-plaintext" style="background: white; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                <strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">المورد:</label>
                            <div class="form-control-plaintext" style="background: white; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                {{ $deliveryNote->supplier->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">المادة:</label>
                            <div class="form-control-plaintext" style="background: white; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                {{ $deliveryNote->material?->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">الكمية المتاحة في المستودع:</label>
                            <div class="form-control-plaintext" style="background: #e3f2fd; border: 2px solid #3498db; padding: 12px; border-radius: 4px; font-weight: bold; color: #3498db; font-size: 16px;">
                                {{ number_format($availableQuantity, 2) }} كيلو
                            </div>
                        </div>
                    </div>

                    <!-- نموذج النقل -->
                    <div class="form-section" style="background: #f0fdf4; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #10b981;">
                        <h5 style="color: #10b981; margin-bottom: 15px; font-weight: 600;">
                            ✅ أدخل الكمية المراد نقلها
                        </h5>

                        <div class="form-group">
                            <label class="form-label">الكمية (كيلو) <span style="color: red;">*</span></label>
                            <div style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex: 1;">
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
                                           style="font-size: 16px; padding: 12px;">
                                    @error('quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="button" class="btn btn-info" id="useFullBtn" style="white-space: nowrap;">
                                    استخدم الكل
                                </button>
                            </div>
                            <small class="form-text text-muted" style="margin-top: 10px; display: block;">
                                <strong>ملاحظة مهمة:</strong>
                                <ul style="margin: 10px 0; padding-left: 20px;">
                                    <li>إذا أدخلت كمية أقل من {{ number_format($availableQuantity, 2) }} كيلو، ستكون هذه <span style="color: #e74c3c; font-weight: bold;">نقل جزئي</span> والحالة تبقى "مسجلة"</li>
                                    <li>فقط عند نقل الكمية الكاملة ({{ number_format($availableQuantity, 2) }} كيلو)، ستتغير الحالة إلى <span style="color: #27ae60; font-weight: bold;">"في الإنتاج"</span></li>
                                    <li>يمكنك نقل البضاعة على عدة مراحل</li>
                                </ul>
                            </small>
                        </div>

                        <!-- عرض الحالة المتوقعة -->
                        <div style="background: white; padding: 15px; border-radius: 4px; border-left: 4px solid #3498db; margin: 15px 0;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <small style="color: #666; display: block; margin-bottom: 5px;">🔄 الحالة بعد النقل:</small>
                                    <div id="statusPreview" style="font-weight: 600; color: #3498db;">
                                        ← سيتم تحديثها عند الإدخال
                                    </div>
                                </div>
                                <div>
                                    <small style="color: #666; display: block; margin-bottom: 5px;">📦 الكمية المتبقية:</small>
                                    <div id="remainingPreview" style="font-weight: 600; color: #27ae60;">
                                        ← سيتم تحديثها عند الإدخال
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="form-group">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea name="notes"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  rows="4"
                                  placeholder="أدخل أي ملاحظات عن عملية النقل..."
                                  style="font-size: 14px;">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الأزرار -->
                    <div style="display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #ddd;">
                        <button type="submit" class="btn btn-success btn-lg" style="flex: 1;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; display: inline; margin-left: 5px;">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            تأكيد النقل
                        </button>
                        <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="btn btn-secondary btn-lg" style="flex: 1;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; display: inline; margin-left: 5px;">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <h3 class="card-title">ℹ️ معلومات مهمة</h3>
            </div>
            <div class="card-body">
                <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; border-right: 4px solid #3498db;">
                    <h6 style="color: #3498db; margin-bottom: 12px; font-weight: 600;">
                        <i class="fas fa-exclamation-circle"></i> فهم آلية النقل:
                    </h6>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                        <li style="margin-bottom: 10px;">
                            <strong>النقل الجزئي:</strong>
                            <span style="color: #555;">عند نقل كمية أقل من المتاح، تبقى الشحنة في حالة "مسجلة" ويمكنك نقل المتبقي لاحقاً</span>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>النقل الكامل:</strong>
                            <span style="color: #555;">عند نقل الكمية الكاملة، تنتقل الشحنة تلقائياً إلى حالة "في الإنتاج"</span>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>سجل الحركات:</strong>
                            <span style="color: #555;">جميع عمليات النقل تُسجل تلقائياً في سجل الحركات</span>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>الدفعات:</strong>
                            <span style="color: #555;">كل شحنة مسجلة تحصل على رقم دفعة فريد للتتبع</span>
                        </li>
                        <li>
                            <strong>المستودع:</strong>
                            <span style="color: #555;">الكميات المنقولة تُخصم تلقائياً من المستودع</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-section {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px 12px;
        font-size: 14px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-control-plaintext {
        padding: 10px;
        border-radius: 4px;
    }

    .btn-lg {
        padding: 12px 20px;
        font-weight: 600;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-success {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .btn-info {
        background: #3498db;
        color: white;
        padding: 10px 15px;
    }

    .btn-info:hover {
        background: #2980b9;
    }

    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        padding: 20px;
    }

    .card-body {
        padding: 20px;
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 13px;
        margin-top: 5px;
    }
</style>

<script>
    const quantityInput = document.getElementById('quantityInput');
    const useFullBtn = document.getElementById('useFullBtn');
    const statusPreview = document.getElementById('statusPreview');
    const remainingPreview = document.getElementById('remainingPreview');

    const availableQuantity = {{ $availableQuantity }};

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
            // تحديد الحالة بناءً على النقل
            const isFullTransfer = Math.abs(entered - availableQuantity) < 0.01;

            if (isFullTransfer) {
                statusPreview.innerHTML = '<span style="background: #27ae60; color: white; padding: 4px 8px; border-radius: 4px;">🏭 في الإنتاج (نقل كامل)</span>';
                statusPreview.style.color = '#27ae60';
            } else {
                statusPreview.innerHTML = '<span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px;">📋 مسجلة (نقل جزئي)</span>';
                statusPreview.style.color = '#3498db';
            }

            remainingPreview.textContent = remaining.toFixed(2) + ' كيلو';
            remainingPreview.style.color = remaining > 0 ? '#f39c12' : '#27ae60';
        } else {
            statusPreview.innerHTML = '← سيتم تحديثها عند الإدخال';
            statusPreview.style.color = '#3498db';
            remainingPreview.textContent = '← سيتم تحديثها عند الإدخال';
            remainingPreview.style.color = '#3498db';
        }
    });
</script>
@endsection
