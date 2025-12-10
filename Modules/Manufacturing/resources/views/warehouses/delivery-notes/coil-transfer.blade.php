@extends('master')

@section('title', 'نقل كويلات للإنتاج')

@section('content')
<!-- مكتبة JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<style>
    .transfer-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .page-header h1 {
        margin: 0;
        font-size: 28px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .form-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-control, select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .form-control:focus, select:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-success {
        background: #4caf50;
        color: white;
    }

    .btn-danger {
        background: #f44336;
        color: white;
    }

    .selected-coils-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        display: none;
    }

    .selected-coils-section.active {
        display: block;
    }

    .selected-coil-item {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 15px;
        align-items: center;
    }

    .coils-table {
        width: 100%;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .coils-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .coils-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        text-align: right;
        font-weight: 600;
    }

    .coils-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        text-align: right;
    }

    .coils-table tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-available {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-partial {
        background: #fff3e0;
        color: #f57c00;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }

    .alert-error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }

    .barcode-card {
        border: 2px solid #4caf50;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        background: white;
        margin-bottom: 15px;
    }

    .barcode-card h4 {
        margin: 0 0 15px 0;
        color: #4caf50;
    }
    
    .barcode-card svg {
        max-width: 100%;
        height: auto;
    }
</style>

<div class="transfer-container">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <span>🔄</span>
            <span>نقل كويلات للإنتاج</span>
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">اختر الكويلات وحدد المرحلة الإنتاجية والموظف المستلم</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
        
        @if(session('production_barcodes'))
            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; color: #333;">✅ تم النقل بنجاح - اطبع الباركودات</h3>
                
                <h4 style="color: #4caf50; margin: 20px 0 15px 0;">باركودات الإنتاج:</h4>
                <div style="display: flex; flex-direction: column; gap: 20px; margin: 20px 0;">
                    @foreach(session('production_barcodes') as $item)
                        <div class="barcode-card">
                            <h4>باركود إنتاج - {{ $item['coil_number'] }}</h4>
                            <svg class="production-barcode" data-barcode="{{ $item['barcode'] }}"></svg>
                            <p style="font-family: monospace; margin: 10px 0; font-size: 11px;">{{ $item['barcode'] }}</p>
                            <p style="font-weight: bold; color: #4caf50;">{{ number_format($item['weight'], 3) }} كجم</p>
                            <button onclick="printBarcode('{{ $item['barcode'] }}', 'إنتاج - {{ $item['coil_number'] }}')" class="btn btn-success" style="margin-top: 10px;">
                                🖨️ طباعة
                            </button>
                        </div>
                    @endforeach
                </div>
                
                @if(session('warehouse_barcodes') && count(session('warehouse_barcodes')) > 0)
                    <h4 style="color: #2196f3; margin: 20px 0 15px 0;">باركودات المتبقي في المستودع:</h4>
                    <div style="display: flex; flex-direction: column; gap: 20px; margin: 20px 0;">
                        @foreach(session('warehouse_barcodes') as $item)
                            <div class="barcode-card" style="border-color: #2196f3;">
                                <h4 style="color: #2196f3;">باركود مستودع - {{ $item['coil_number'] }}</h4>
                                <svg class="warehouse-barcode" data-barcode="{{ $item['barcode'] }}"></svg>
                                <p style="font-family: monospace; margin: 10px 0; font-size: 11px;">{{ $item['barcode'] }}</p>
                                <p style="font-weight: bold; color: #2196f3;">{{ number_format($item['weight'], 3) }} كجم</p>
                                <button onclick="printBarcode('{{ $item['barcode'] }}', 'مستودع - {{ $item['coil_number'] }}')" class="btn btn-primary" style="margin-top: 10px;">
                                    🖨️ طباعة
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <span>✗</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Scanner Section -->
    <div class="form-section">
        <h3 style="margin: 0 0 20px 0; color: #333;">🔍 مسح باركود الكويل</h3>
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <input type="text" 
                       id="barcodeInput" 
                       class="form-control" 
                       placeholder="امسح الباركود أو أدخله يدوياً"
                       autofocus>
            </div>
            <button type="button" onclick="scanBarcode()" class="btn btn-primary">
                🔍 بحث
            </button>
        </div>
    </div>

    <!-- Selected Coils Section -->
    <div id="selectedCoilsSection" class="selected-coils-section">
        <h3 style="margin: 0 0 20px 0; color: #333;">📦 الكويلات المحددة (<span id="selectedCount">0</span>)</h3>
        <div id="selectedCoilsList"></div>
        
        <!-- Transfer Form -->
        <form method="POST" action="{{ route('manufacturing.coils.transfer') }}" id="transferForm">
            @csrf
            <div id="coilsInputsContainer"></div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="form-group">
                    <label for="production_stage">المرحلة الإنتاجية *</label>
                    <select name="production_stage" id="production_stage" class="form-control" required>
                        <option value="">-- اختر المرحلة --</option>
                        @foreach($productionStages as $stage)
                            <option value="{{ $stage->stage_code }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="assigned_to">الموظف المستلم *</label>
                    <select name="assigned_to" id="assigned_to" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">ملاحظات</label>
                <textarea name="notes" 
                          id="notes" 
                          class="form-control" 
                          rows="3" 
                          placeholder="أضف ملاحظات إضافية (اختياري)"></textarea>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    ✓ نقل للإنتاج
                </button>
                <button type="button" onclick="clearSelection()" class="btn btn-danger">
                    ✗ مسح الكل
                </button>
            </div>
        </form>
    </div>

    <!-- Available Coils Table -->
    <div class="coils-table">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                    </th>
                    <th>#</th>
                    <th>رقم الكويل</th>
                    <th>الباركود</th>
                    <th>المادة</th>
                    <th>المستودع</th>
                    <th>الوزن الأصلي</th>
                    <th>الوزن المتبقي</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($availableCoils as $index => $coil)
                    <tr id="row-{{ $coil->id }}">
                        <td>
                            <input type="checkbox" 
                                   class="coil-checkbox" 
                                   data-coil-id="{{ $coil->id }}"
                                   onchange="toggleCoilSelection(this)">
                        </td>
                        <td>{{ ($availableCoils->currentPage() - 1) * $availableCoils->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $coil->coil_number }}</strong></td>
                        <td><code style="font-size: 11px;">{{ $coil->coil_barcode }}</code></td>
                        <td>{{ $coil->deliveryNote->material->material_name ?? 'غير محدد' }}</td>
                        <td>{{ $coil->deliveryNote->warehouse->warehouse_name ?? 'غير محدد' }}</td>
                        <td>{{ number_format($coil->coil_weight, 3) }} كجم</td>
                        <td><strong style="color: #4caf50;">{{ number_format($coil->remaining_weight, 3) }} كجم</strong></td>
                        <td>
                            @if($coil->status === 'available')
                                <span class="status-badge status-available">✓ متاح</span>
                            @else
                                <span class="status-badge status-partial">⚡ مستخدم جزئياً</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" 
                                    onclick="quickAddCoil({{ json_encode([
                                        'id' => $coil->id,
                                        'coil_number' => $coil->coil_number,
                                        'coil_barcode' => $coil->coil_barcode,
                                        'remaining_weight' => $coil->remaining_weight,
                                        'material_name' => $coil->deliveryNote->material->material_name ?? 'غير محدد',
                                    ]) }})" 
                                    class="btn btn-primary" 
                                    style="padding: 8px 15px; font-size: 14px;">
                                إضافة
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
                            <p style="margin: 0;">لا توجد كويلات متاحة للنقل</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($availableCoils->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $availableCoils->links() }}
        </div>
    @endif
</div>

<script>
let selectedCoils = [];

// مسح الباركود
function scanBarcode() {
    const barcode = document.getElementById('barcodeInput').value.trim();
    
    if (!barcode) {
        alert('الرجاء إدخال الباركود');
        return;
    }

    fetch(`{{ route('manufacturing.coils.scan') }}?barcode=${encodeURIComponent(barcode)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                quickAddCoil(data.coil);
                document.getElementById('barcodeInput').value = '';
            } else {
                alert(data.message || 'الباركود غير موجود');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في البحث');
        });
}

// Enter key للبحث
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        scanBarcode();
    }
});

// إضافة كويل سريعاً
function quickAddCoil(coil) {
    // التحقق من عدم التكرار
    if (selectedCoils.find(c => c.id === coil.id)) {
        alert('هذا الكويل مضاف بالفعل');
        return;
    }

    selectedCoils.push({
        id: coil.id,
        coil_number: coil.coil_number,
        coil_barcode: coil.coil_barcode,
        remaining_weight: coil.remaining_weight,
        material_name: coil.material_name,
        transfer_weight: coil.remaining_weight // الافتراضي: كل الوزن
    });

    updateSelectedCoilsUI();
}

// تحديث واجهة الكويلات المحددة
function updateSelectedCoilsUI() {
    const section = document.getElementById('selectedCoilsSection');
    const list = document.getElementById('selectedCoilsList');
    const count = document.getElementById('selectedCount');
    const container = document.getElementById('coilsInputsContainer');

    count.textContent = selectedCoils.length;

    if (selectedCoils.length > 0) {
        section.classList.add('active');
        
        // عرض القائمة
        list.innerHTML = selectedCoils.map((coil, index) => `
            <div class="selected-coil-item">
                <div>
                    <strong>${coil.coil_number}</strong>
                    <br>
                    <small style="color: #666;">${coil.material_name}</small>
                </div>
                <div>
                    <small style="color: #666;">الوزن المتبقي</small>
                    <br>
                    <strong>${parseFloat(coil.remaining_weight).toFixed(3)} كجم</strong>
                </div>
                <div>
                    <label style="font-size: 12px; margin-bottom: 5px;">الوزن المطلوب نقله (كجم)</label>
                    <input type="number" 
                           class="form-control" 
                           value="${coil.transfer_weight}" 
                           step="0.001" 
                           min="0.001" 
                           max="${coil.remaining_weight}"
                           onchange="updateTransferWeight(${index}, this.value)"
                           style="padding: 8px;">
                </div>
                <button type="button" onclick="removeCoil(${index})" class="btn btn-danger" style="padding: 8px 15px;">
                    ✗
                </button>
            </div>
        `).join('');

        // إنشاء حقول مخفية للإرسال
        container.innerHTML = selectedCoils.map((coil, index) => `
            <input type="hidden" name="coils[${index}][coil_id]" value="${coil.id}">
            <input type="hidden" name="coils[${index}][transfer_weight]" value="${coil.transfer_weight}" id="hidden-weight-${index}">
        `).join('');
    } else {
        section.classList.remove('active');
        list.innerHTML = '';
        container.innerHTML = '';
    }
}

// تحديث وزن النقل
function updateTransferWeight(index, value) {
    selectedCoils[index].transfer_weight = parseFloat(value);
    document.getElementById(`hidden-weight-${index}`).value = value;
}

// حذف كويل
function removeCoil(index) {
    selectedCoils.splice(index, 1);
    updateSelectedCoilsUI();
}

// مسح الكل
function clearSelection() {
    if (confirm('هل أنت متأكد من مسح جميع الكويلات المحددة؟')) {
        selectedCoils = [];
        updateSelectedCoilsUI();
        
        // إلغاء تحديد الـ checkboxes
        document.querySelectorAll('.coil-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
    }
}

// تحديد/إلغاء تحديد كويل من checkbox
function toggleCoilSelection(checkbox) {
    const coilId = parseInt(checkbox.dataset.coilId);
    
    if (checkbox.checked) {
        // البحث عن بيانات الكويل في الجدول
        const row = checkbox.closest('tr');
        const coilNumber = row.cells[2].textContent.trim();
        const barcode = row.cells[3].textContent.trim();
        const material = row.cells[4].textContent.trim();
        const remaining = parseFloat(row.cells[7].textContent.replace(' كجم', '').replace(',', ''));
        
        quickAddCoil({
            id: coilId,
            coil_number: coilNumber,
            coil_barcode: barcode,
            material_name: material,
            remaining_weight: remaining
        });
    } else {
        const index = selectedCoils.findIndex(c => c.id === coilId);
        if (index > -1) {
            removeCoil(index);
        }
    }
}

// تحديد/إلغاء تحديد الكل
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.coil-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
        toggleCoilSelection(cb);
    });
}

// طباعة باركود
function printBarcode(barcode, title) {
    const printWindow = window.open('', '_blank', 'width=400,height=300');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة باركود - ${title}</title>
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
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            <div class="barcode-container">
                <h3>🏭 مصنع الحديد</h3>
                <h4>${title}</h4>
                <svg id="printBarcode"></svg>
                <p style="font-size: 11px; color: #666; margin-top: 10px;">تاريخ الطباعة: ${new Date().toLocaleString('ar-EG')}</p>
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
}

// توليد الباركودات في الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // توليد باركودات الإنتاج
    document.querySelectorAll('.production-barcode').forEach(function(svg) {
        const code = svg.getAttribute('data-barcode');
        JsBarcode(svg, code, {
            format: "CODE128",
            width: 2,
            height: 60,
            displayValue: false,
            margin: 5
        });
    });
    
    // توليد باركودات المستودع
    document.querySelectorAll('.warehouse-barcode').forEach(function(svg) {
        const code = svg.getAttribute('data-barcode');
        JsBarcode(svg, code, {
            format: "CODE128",
            width: 2,
            height: 60,
            displayValue: false,
            margin: 5
        });
    });
});
</script>
@endsection
