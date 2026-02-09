@extends('master')

@section('title', 'إذن تسليم جديد')

@section('content')
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
    <!-- مؤشر الخطوات -->
    <div class="step-indicator">
        <div class="step-number">1</div>
        <div style="flex: 1;">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">📋 إنشاء أذن تسليم</div>
            <div style="opacity: 0.9;">بعد الحفظ، ستنتقل تلقائياً للخطوة الثانية: التسجيل في المستودع وتوليد الباركود</div>
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
            <strong>خطأ في البيانات:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacturing.delivery-notes.store') }}" id="deliveryForm">
        @csrf

        <!-- نوع الأذن -->
        <input type="hidden" name="type" value="incoming">
        <div class="simple-card">
            <div class="type-selector" style="width: 100%; display: block;">
                <div class="type-option" style="cursor: default; width: 100%; display: block;">
                    <div class="type-content" style="width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px 0;">
                        <div class="type-icon" style="font-size: 48px;">📥</div>
                        <div class="type-text" style="font-size: 26px; margin-top: 10px;">واردة</div>
                        <small style="font-size: 16px; margin-top: 5px;">من المورد</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- البيانات الأساسية -->
        <div class="simple-card">
            <div class="card-title">
                📋 البيانات الأساسية
            </div>
            
            <div class="form-group-simple">
                <label class="label-simple">📌 رقم الأذن</label>
                <input type="text" class="input-simple" value="سيتم توليده تلقائياً" disabled>
                <div class="helper-text">
                    ✓ لا تقلق، سيتم توليد رقم فريد تلقائياً عند الحفظ
                </div>
            </div>
            
            <div class="form-group-simple">
                <label class="label-simple">📅 التاريخ <span class="required-mark">*</span></label>
                <input type="date" name="delivery_date" class="input-simple" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <!-- بيانات الشحنة الواردة -->
        <div class="simple-card" id="incomingCard">
            <div class="card-title">
                📥 بيانات الشحنة الواردة
            </div>
            
            <div class="form-group-simple">
                <label class="label-simple">🏢 المستودع <span class="required-mark">*</span></label>
                <select name="warehouse_id" id="warehouseSelect" class="input-simple" required>
                    <option value="">اختر المستودع</option>
                    @foreach($warehouses ?? [] as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group-simple">
                <label class="label-simple">📦 المادة <span class="required-mark">*</span></label>
                <select name="material_id" id="materialSelect" class="input-simple" required>
                    <option value="">اختر المادة</option>
                    @foreach($materials ?? [] as $material)
                        <option value="{{ $material->id }}">{{ $material->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group-simple">
                <label class="label-simple">⚖️ الكمية <span class="required-mark">*</span></label>
                <input type="number" name="quantity" class="input-simple" placeholder="أدخل الكمية" step="0.01" min="0.01" required>
                <div class="helper-text">
                    ✓ سيتم تسجيلها في المستودع تلقائياً
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit-simple">
                <span style="font-size: 24px;">✓</span>
                <span>حفظ الأذن والانتقال للخطوة التالية</span>
            </button>
            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn-cancel-simple">
                ✕ إلغاء
            </a>
        </div>
    </form>
</div>

@endsection
