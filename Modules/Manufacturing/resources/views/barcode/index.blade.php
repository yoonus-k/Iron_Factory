@extends('master')

@section('content')
<style>
    .barcode-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .barcode-header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-right: 4px solid #3498db;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .barcode-header h1 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .barcode-header p {
        color: #7f8c8d;
        font-size: 14px;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 18px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: box-shadow 0.2s;
    }

    .stat-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .stat-card .label {
        color: #7f8c8d;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .stat-card .value {
        color: #2c3e50;
        font-size: 26px;
        font-weight: 600;
    }

    .stat-card.total { border-right: 3px solid #3498db; }
    .stat-card.active { border-right: 3px solid #27ae60; }
    .stat-card.used { border-right: 3px solid #e67e22; }
    .stat-card.scans { border-right: 3px solid #9b59b6; }

    .content-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .content-section h2 {
        color: #2c3e50;
        font-size: 18px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ecf0f1;
        font-weight: 600;
    }

    .barcode-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .barcode-table th {
        background: #34495e;
        color: white;
        padding: 12px 10px;
        text-align: right;
        font-weight: 600;
        font-size: 13px;
    }

    .barcode-table th:first-child {
        border-radius: 6px 0 0 0;
    }

    .barcode-table th:last-child {
        border-radius: 0 6px 0 0;
    }

    .barcode-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #ecf0f1;
        color: #2c3e50;
    }

    .barcode-table tr:last-child td {
        border-bottom: none;
    }

    .barcode-table tr:hover {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge.active {
        background: #d4edda;
        color: #155724;
    }

    .badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .type-badge {
        background: #3498db;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .btn {
        padding: 8px 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
        font-weight: 500;
    }

    .btn-edit {
        background: #3498db;
        color: white;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .btn-primary {
        background: #27ae60;
        color: white;
        padding: 10px 18px;
    }

    .btn-primary:hover {
        background: #229954;
    }

    .barcode-types {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }

    .barcode-type {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
        border: 1px solid #ecf0f1;
    }

    .barcode-type .type {
        color: #3498db;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .barcode-type .count {
        color: #2c3e50;
        font-size: 22px;
        font-weight: 600;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideDown 0.3s;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 2px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        color: #2c3e50;
        font-size: 18px;
        margin: 0;
        font-weight: 600;
    }

    .modal-close {
        background: #e74c3c;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #c0392b;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        color: #2c3e50;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
    }

    .form-group input[type="checkbox"] {
        width: auto;
        margin-left: 8px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 2px solid #ecf0f1;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-success {
        background: #27ae60;
        color: white;
        padding: 10px 20px;
    }

    .btn-success:hover {
        background: #229954;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
        padding: 10px 20px;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-add {
        background: #3498db;
        color: white;
        padding: 10px 18px;
    }

    .btn-add:hover {
        background: #2980b9;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .toast {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #27ae60;
        color: white;
        padding: 15px 25px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 2000;
        display: none;
        animation: slideDown 0.3s;
    }

    .toast.error {
        background: #e74c3c;
    }

    .toast.active {
        display: block;
    }
</style>

<div class="barcode-container">
    <!-- Header -->
    <div class="barcode-header">
        <h1>🔢 إدارة نظام الباركود</h1>
        <p>إدارة وتخصيص إعدادات الباركود لجميع المراحل الإنتاجية</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="label">إجمالي الباركودات</div>
            <div class="value">{{ number_format($statistics['total']) }}</div>
        </div>
        <div class="stat-card active">
            <div class="label">الباركودات النشطة</div>
            <div class="value">{{ number_format($statistics['active']) }}</div>
        </div>
        <div class="stat-card used">
            <div class="label">الباركودات المستخدمة</div>
            <div class="value">{{ number_format($statistics['used']) }}</div>
        </div>
        <div class="stat-card scans">
            <div class="label">إجمالي عمليات المسح</div>
            <div class="value">{{ number_format($statistics['total_scans']) }}</div>
        </div>
    </div>

    <!-- Barcode Types Distribution -->
    <div class="content-section">
        <h2>توزيع الباركودات حسب النوع</h2>
        <div class="barcode-types">
            @foreach($statistics['by_type'] as $type => $count)
            <div class="barcode-type">
                <div class="type">
                    @switch($type)
                        @case('raw_material') مواد خام @break
                        @case('stage1') المرحلة 1 @break
                        @case('stage2') المرحلة 2 @break
                        @case('stage3') المرحلة 3 @break
                        @case('stage4') المرحلة 4 @break
                        @default {{ $type }}
                    @endswitch
                </div>
                <div class="count">{{ number_format($count) }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Settings Table -->
    <div class="content-section">
        <h2>إعدادات الباركود</h2>
        <div class="actions">
            <button class="btn btn-add" onclick="addBarcodeSetting()">
                ➕ إضافة نوع باركود جديد
            </button>
            <button class="btn btn-primary" onclick="resetYear()">
                🔄 إعادة تعيين السنة الجديدة
            </button>
        </div>
        <table class="barcode-table">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>البادئة</th>
                    <th>آخر رقم</th>
                    <th>السنة</th>
                    <th>الصيغة</th>
                    <th>الأصفار</th>
                    <th>زيادة تلقائية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settings as $setting)
                <tr>
                    <td>
                        <span class="type-badge">
                            @switch($setting->type)
                                @case('raw_material') مواد خام @break
                                @case('stage1') استاندات @break
                                @case('stage2') معالجة @break
                                @case('stage3') كويلات @break
                                @case('stage4') صناديق @break
                                @default {{ $setting->type }}
                            @endswitch
                        </span>
                    </td>
                    <td><strong>{{ $setting->prefix }}</strong></td>
                    <td>{{ str_pad($setting->current_number, $setting->padding, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $setting->year }}</td>
                    <td><code style="background:#ecf0f1;padding:3px 8px;border-radius:4px;font-size:12px;">{{ $setting->format }}</code></td>
                    <td>{{ $setting->padding }}</td>
                    <td>
                        @if($setting->auto_increment)
                            <span style="color:#27ae60;font-size:16px;">✓</span>
                        @else
                            <span style="color:#e74c3c;font-size:16px;">✗</span>
                        @endif
                    </td>
                    <td>
                        @if($setting->is_active)
                            <span class="badge active">نشط</span>
                        @else
                            <span class="badge inactive">غير نشط</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-edit" onclick="editSetting({{ $setting->id }})">
                            ✏️ تعديل
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>✏️ تعديل إعدادات الباركود</h3>
            <button class="modal-close" onclick="closeModal('editModal')">×</button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="edit_id">
                
                <div class="form-group">
                    <label>النوع</label>
                    <input type="text" id="edit_type" readonly style="background:#f8f9fa;">
                </div>

                <div class="form-group">
                    <label>البادئة <span style="color:#e74c3c;">*</span></label>
                    <input type="text" id="edit_prefix" maxlength="10" required placeholder="مثال: RW">
                </div>

                <div class="form-group">
                    <label>الصيغة</label>
                    <input type="text" id="edit_format" readonly value="{prefix}-{year}-{number}" style="background:#f8f9fa;cursor:not-allowed;">
                    <small style="color:#7f8c8d;font-size:12px;">الصيغة ثابتة ولا يمكن تعديلها</small>
                </div>

                <div class="form-group">
                    <label>عدد الأصفار <span style="color:#e74c3c;">*</span></label>
                    <input type="number" id="edit_padding" min="1" max="10" required>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="edit_auto_increment">
                        زيادة تلقائية
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="edit_is_active">
                        نشط
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('editModal')">إلغاء</button>
            <button class="btn btn-success" onclick="saveEdit()">💾 حفظ التعديلات</button>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>➕ إضافة نوع باركود جديد</h3>
            <button class="modal-close" onclick="closeModal('addModal')">×</button>
        </div>
        <div class="modal-body">
            <form id="addForm">
                <div class="form-group">
                    <label>النوع <span style="color:#e74c3c;">*</span></label>
                    <input type="text" id="add_type" required placeholder="مثال: warehouse">
                    <small style="color:#7f8c8d;font-size:12px;">اسم فريد بالإنجليزية</small>
                </div>

                <div class="form-group">
                    <label>البادئة <span style="color:#e74c3c;">*</span></label>
                    <input type="text" id="add_prefix" maxlength="10" required placeholder="مثال: WH">
                </div>

                <div class="form-group">
                    <label>الصيغة</label>
                    <input type="text" id="add_format" readonly value="{prefix}-{year}-{number}" style="background:#f8f9fa;cursor:not-allowed;">
                    <small style="color:#7f8c8d;font-size:12px;">الصيغة ثابتة ولا يمكن تعديلها</small>
                </div>

                <div class="form-group">
                    <label>عدد الأصفار <span style="color:#e74c3c;">*</span></label>
                    <input type="number" id="add_padding" value="3" min="1" max="10" required>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="add_auto_increment" checked>
                        زيادة تلقائية
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="add_is_active" checked>
                        نشط
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('addModal')">إلغاء</button>
            <button class="btn btn-success" onclick="saveAdd()">💾 إضافة</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script>
    // Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type + ' active';
        
        setTimeout(() => {
            toast.classList.remove('active');
        }, 3000);
    }

    // Edit Setting
    function editSetting(id) {
        // جلب البيانات من الجدول
        const row = event.target.closest('tr');
        const cells = row.cells;
        
        // استخراج البيانات
        const type = cells[0].querySelector('.type-badge').textContent.trim();
        const prefix = cells[1].textContent.trim();
        const currentNumber = cells[2].textContent.trim();
        const year = cells[3].textContent.trim();
        const format = cells[4].querySelector('code').textContent.trim();
        const padding = cells[5].textContent.trim();
        const autoIncrement = cells[6].querySelector('span').textContent === '✓';
        const isActive = cells[7].querySelector('.badge').classList.contains('active');

        // ملء النموذج
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_prefix').value = prefix;
        document.getElementById('edit_format').value = format;
        document.getElementById('edit_padding').value = padding;
        document.getElementById('edit_auto_increment').checked = autoIncrement;
        document.getElementById('edit_is_active').checked = isActive;

        openModal('editModal');
    }

    // Save Edit
    function saveEdit() {
        const id = document.getElementById('edit_id').value;
        const data = {
            prefix: document.getElementById('edit_prefix').value,
            format: '{prefix}-{year}-{number}', // صيغة ثابتة
            padding: parseInt(document.getElementById('edit_padding').value),
            auto_increment: document.getElementById('edit_auto_increment').checked,
            is_active: document.getElementById('edit_is_active').checked,
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showToast('❌ خطأ: CSRF Token غير موجود', 'error');
            return;
        }

        fetch(`/barcode/settings/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✅ ' + data.message);
                closeModal('editModal');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('❌ حدث خطأ: ' + error.message, 'error');
        });
    }

    // Add Barcode Setting
    function addBarcodeSetting() {
        // تنظيف النموذج
        document.getElementById('addForm').reset();
        document.getElementById('add_format').value = '{prefix}-{year}-{number}';
        document.getElementById('add_padding').value = 3;
        document.getElementById('add_auto_increment').checked = true;
        document.getElementById('add_is_active').checked = true;
        
        openModal('addModal');
    }

    // Save Add
    function saveAdd() {
        const data = {
            type: document.getElementById('add_type').value,
            prefix: document.getElementById('add_prefix').value,
            format: '{prefix}-{year}-{number}', // صيغة ثابتة
            padding: parseInt(document.getElementById('add_padding').value),
            auto_increment: document.getElementById('add_auto_increment').checked,
            is_active: document.getElementById('add_is_active').checked,
        };

        // التحقق من البيانات
        if (!data.type || !data.prefix) {
            showToast('❌ يرجى ملء جميع الحقول المطلوبة', 'error');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showToast('❌ خطأ: CSRF Token غير موجود', 'error');
            return;
        }

        fetch('/barcode/settings/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✅ ' + data.message);
                closeModal('addModal');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('❌ حدث خطأ: ' + error.message, 'error');
        });
    }

    // Reset Year
    function resetYear() {
        if (confirm('هل أنت متأكد من إعادة تعيين الأرقام للسنة الجديدة؟')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                showToast('❌ خطأ: CSRF Token غير موجود', 'error');
                return;
            }

            fetch('/barcode/reset-year', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ ' + data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                showToast('❌ حدث خطأ: ' + error.message, 'error');
            });
        }
    }

    // Close modal on outside click
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }
</script>
@endsection
