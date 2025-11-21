@extends('master')

@section('title', 'لوحة تحكم التسوية والربط')

@section('content')
<div class="container-fluid">
    <!-- رأس الصفحة -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-secondary">
                    ← رجوع
                </a>
            </div>
            <div class="col">
                <h1 class="page-title">⚙️ لوحة تحكم التسوية والربط</h1>
                <p class="text-muted">إدارة شاملة - بحث وتعديل وحذف الأذونات والفواتير</p>
            </div>
        </div>
    </div>

    <!-- الرسائل -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- التبويبات الرئيسية -->
    <ul class="nav nav-tabs mb-4" id="managementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="delivery-notes-tab" data-bs-toggle="tab"
                data-bs-target="#delivery-notes-pane" type="button" role="tab">
                📦 إدارة الأذونات
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="invoices-tab" data-bs-toggle="tab"
                data-bs-target="#invoices-pane" type="button" role="tab">
                📄 إدارة الفواتير
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reconciliation-tab" data-bs-toggle="tab"
                data-bs-target="#reconciliation-pane" type="button" role="tab">
                ⚖️ سجلات التسوية
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="movements-tab" data-bs-toggle="tab"
                data-bs-target="#movements-pane" type="button" role="tab">
                🔄 الحركات المسجلة
            </button>
        </li>
    </ul>

    <div class="tab-content" id="managementTabContent">
        <!-- ===================== تبويب الأذونات ===================== -->
        <div class="tab-pane fade show active" id="delivery-notes-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📦 إدارة أذونات التسليم</h5>
                    <div>
                        <input type="text" id="deliveryNotesSearch" class="form-control d-inline"
                            style="width: 250px;" placeholder="ابحث عن أذن...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="deliveryNotesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الأذن</th>
                                    <th>المورد</th>
                                    <th>الوزن الفعلي</th>
                                    <th>تاريخ الاستلام</th>
                                    <th>الحالة</th>
                                    <th>الفاتورة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- سيتم الملء من قاعدة البيانات -->
                            </tbody>
                        </table>
                        <div class="alert alert-info mt-3 mb-0">
                            📌 الأذونات التي لم ترتبط بفاتورة بعد
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== تبويب الفواتير ===================== -->
        <div class="tab-pane fade" id="invoices-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📄 إدارة الفواتير المرتبطة</h5>
                    <div>
                        <input type="text" id="invoicesSearch" class="form-control d-inline"
                            style="width: 250px;" placeholder="ابحث عن فاتورة...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="invoicesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>المورد</th>
                                    <th>تاريخ الفاتورة</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>عدد الأذونات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- سيتم الملء من قاعدة البيانات -->
                            </tbody>
                        </table>
                        <div class="alert alert-info mt-3 mb-0">
                            📌 الفواتير المرتبطة بأذونات التسليم
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== تبويب سجلات التسوية ===================== -->
        <div class="tab-pane fade" id="reconciliation-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">⚖️ سجلات التسوية والربط</h5>
                    <div>
                        <select id="reconciliationStatusFilter" class="form-select d-inline" style="width: 200px;">
                            <option value="">جميع الحالات</option>
                            <option value="pending">قيد الانتظار</option>
                            <option value="matched">متطابقة</option>
                            <option value="discrepancy">بها فروقات</option>
                            <option value="adjusted">تم التعديل</option>
                            <option value="rejected">مرفوضة</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="reconciliationTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الأذن</th>
                                    <th>الفاتورة</th>
                                    <th>الوزن الفعلي</th>
                                    <th>وزن الفاتورة</th>
                                    <th>الفرق</th>
                                    <th>النسبة %</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- سيتم الملء من قاعدة البيانات -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== تبويب الحركات ===================== -->
        <div class="tab-pane fade" id="movements-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🔄 الحركات المسجلة من التسوية</h5>
                    <div>
                        <input type="text" id="movementsSearch" class="form-control d-inline"
                            style="width: 250px;" placeholder="ابحث عن حركة...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="movementsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الحركة</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>المصدر</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- سيتم الملء من قاعدة البيانات -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================== Modals ======================== -->

<!-- Modal: تأكيد الحذف -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">⚠️ تأكيد الحذف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ حذف نهائياً</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // ==================== تحميل بيانات الأذونات ====================
    function loadDeliveryNotes() {
        $.ajax({
            url: '{{ route("manufacturing.warehouses.reconciliation.api.get-delivery-notes") }}',
            dataType: 'json',
            success: function(data) {
                let html = '';
                data.forEach(function(note, index) {
                    const statusBadge = getStatusBadge(note.registration_status);
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>#${note.note_number}</strong></td>
                            <td>${note.supplier_name}</td>
                            <td>${parseFloat(note.actual_weight).toFixed(2)} كجم</td>
                            <td>${note.delivery_date}</td>
                            <td>${statusBadge}</td>
                            <td>${note.purchase_invoice_id ? '✅ مرتبطة' : '❌ غير مرتبطة'}</td>
                            <td>
                                <a href="{{ route('manufacturing.warehouses.reconciliation.edit-delivery-note', '') }}/${note.id}"
                                   class="btn btn-sm btn-warning">✏️</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteDeliveryNote(${note.id}, '${note.note_number}')">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#deliveryNotesTable tbody').html(html);
            }
        });
    }

    // ==================== تحميل بيانات الفواتير ====================
    function loadInvoices() {
        $.ajax({
            url: '{{ route("manufacturing.warehouses.reconciliation.api.get-invoices") }}',
            dataType: 'json',
            success: function(data) {
                let html = '';
                data.forEach(function(invoice, index) {
                    const statusBadge = getInvoiceStatusBadge(invoice.status);
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>#${invoice.invoice_number}</strong></td>
                            <td>${invoice.supplier_name}</td>
                            <td>${invoice.invoice_date}</td>
                            <td>${parseFloat(invoice.total_amount).toFixed(2)}</td>
                            <td>${statusBadge}</td>
                            <td><span class="badge bg-primary">${invoice.delivery_notes_count}</span></td>
                            <td>
                                <a href="{{ route('manufacturing.warehouses.reconciliation.edit-invoice', '') }}/${invoice.id}"
                                   class="btn btn-sm btn-warning">✏️</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteInvoice(${invoice.id}, '${invoice.invoice_number}')">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#invoicesTable tbody').html(html);
            }
        });
    }

    // ==================== تحميل سجلات التسوية ====================
    function loadReconciliationLogs() {
        $.ajax({
            url: '{{ route("manufacturing.warehouses.reconciliation.api.get-reconciliation-logs") }}',
            dataType: 'json',
            success: function(data) {
                let html = '';
                data.forEach(function(log, index) {
                    const statusBadge = getStatusBadge(log.reconciliation_status);
                    const discrepancyColor = log.discrepancy > 0 ? 'text-danger' : 'text-success';
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>#${log.note_number}</td>
                            <td>#${log.invoice_number}</td>
                            <td>${parseFloat(log.actual_weight).toFixed(2)} كجم</td>
                            <td>${parseFloat(log.invoice_weight).toFixed(2)} كجم</td>
                            <td class="${discrepancyColor}">${parseFloat(log.discrepancy).toFixed(2)} كجم</td>
                            <td>${parseFloat(log.discrepancy_percentage).toFixed(2)}%</td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="{{ route('manufacturing.warehouses.reconciliation.edit-reconciliation', '') }}/${log.id}"
                                   class="btn btn-sm btn-warning">✏️</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteReconciliation(${log.id})">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#reconciliationTable tbody').html(html);
            }
        });
    }

    // ==================== تحميل الحركات ====================
    function loadMovements() {
        $.ajax({
            url: '{{ route("manufacturing.warehouses.reconciliation.api.get-movements") }}',
            dataType: 'json',
            success: function(data) {
                let html = '';
                data.forEach(function(movement, index) {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>#${movement.movement_number}</strong></td>
                            <td>${getMovementType(movement.movement_type)}</td>
                            <td>${parseFloat(movement.quantity).toFixed(2)} كجم</td>
                            <td>${movement.source}</td>
                            <td>${movement.movement_date}</td>
                            <td><span class="badge bg-success">${movement.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteMovement(${movement.id}, '${movement.movement_number}')">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#movementsTable tbody').html(html);
            }
        });
    }

    // ==================== دوال مساعدة ====================
    function getStatusBadge(status) {
        const statuses = {
            'registered': '<span class="badge bg-success">✅ مسجلة</span>',
            'pending': '<span class="badge bg-warning">⏳ قيد الانتظار</span>',
            'in_production': '<span class="badge bg-info">🏭 في الإنتاج</span>',
            'matched': '<span class="badge bg-success">✓ متطابقة</span>',
            'discrepancy': '<span class="badge bg-warning">⚠️ فروقات</span>',
            'adjusted': '<span class="badge bg-info">📝 تم التعديل</span>',
            'rejected': '<span class="badge bg-danger">❌ مرفوضة</span>',
        };
        return statuses[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    function getInvoiceStatusBadge(status) {
        const statuses = {
            'pending': '<span class="badge bg-warning">⏳ قيد الانتظار</span>',
            'processed': '<span class="badge bg-info">⚙️ معالج</span>',
            'completed': '<span class="badge bg-success">✅ مكتمل</span>',
            'cancelled': '<span class="badge bg-danger">❌ ملغي</span>',
        };
        return statuses[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    function getMovementType(type) {
        const types = {
            'adjustment': '⚙️ تعديل',
            'reconciliation': '⚖️ تسوية',
            'to_production': '🏭 نقل إنتاج',
            'warehouse_transfer': '🔄 تحويل مخزن',
        };
        return types[type] || type;
    }

    // ==================== حذف ====================
    window.deleteDeliveryNote = function(id, noteNumber) {
        $('#deleteMessage').html(`هل أنت متأكد من حذف الأذن <strong>#${noteNumber}</strong>؟`);
        $('#deleteForm').attr('action', `{{ route('manufacturing.warehouses.reconciliation.delete-delivery-note', '') }}/${id}`);
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    };

    window.deleteInvoice = function(id, invoiceNumber) {
        $('#deleteMessage').html(`هل أنت متأكد من حذف الفاتورة <strong>#${invoiceNumber}</strong>؟`);
        $('#deleteForm').attr('action', `{{ route('manufacturing.warehouses.reconciliation.delete-invoice', '') }}/${id}`);
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    };

    window.deleteReconciliation = function(id) {
        $('#deleteMessage').html(`هل أنت متأكد من حذف هذا السجل؟`);
        $('#deleteForm').attr('action', `{{ route('manufacturing.warehouses.reconciliation.delete-reconciliation', '') }}/${id}`);
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    };

    window.deleteMovement = function(id, movementNumber) {
        $('#deleteMessage').html(`هل أنت متأكد من حذف الحركة <strong>#${movementNumber}</strong>؟`);
        $('#deleteForm').attr('action', `{{ route('manufacturing.warehouses.reconciliation.delete-movement', '') }}/${id}`);
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    };

    // ==================== البحث ====================
    $('#deliveryNotesSearch').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('#deliveryNotesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
        });
    });

    $('#invoicesSearch').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('#invoicesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
        });
    });

    $('#movementsSearch').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('#movementsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
        });
    });

    // ==================== التحميل الأولي ====================
    loadDeliveryNotes();
    loadInvoices();
    loadReconciliationLogs();
    loadMovements();

    // ==================== تحديث البيانات عند تبديل التبويبات ====================
    $('#managementTabs button').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('data-bs-target');
        if (target === '#delivery-notes-pane') loadDeliveryNotes();
        else if (target === '#invoices-pane') loadInvoices();
        else if (target === '#reconciliation-pane') loadReconciliationLogs();
        else if (target === '#movements-pane') loadMovements();
    });
});
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
    }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .nav-tabs .nav-link.active {
        color: #3498db;
        border-bottom: 3px solid #3498db;
    }
</style>
@endsection
