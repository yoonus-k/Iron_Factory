@extends('master')

@section('title', 'مسح الباركود - المرحلة الأولى')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-barcode"></i> مسح الباركود
            </h1>
            <p class="text-muted">قم بمسح باركود الاستاند لتتبع تقدم المرحلة الأولى</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-qrcode"></i> ماسح الباركود
                    </h5>
                </div>
                <div class="card-body">
                    <form id="barcodeForm" method="POST" action="{{ route('manufacturing.stage1.process-barcode') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="barcode" class="form-label">رقم الباركود:</label>
                            <input
                                type="text"
                                id="barcode"
                                name="barcode"
                                class="form-control form-control-lg"
                                placeholder="اضغط هنا وقم بمسح الباركود..."
                                autofocus
                                required
                            >
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check"></i> تأكيد
                            </button>
                        </div>
                    </form>

                    <div id="scanResults" class="mt-4" style="display: none;">
                        <h5>نتائج المسح الأخيرة:</h5>
                        <div id="resultsList" class="list-group"></div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> سجل المسح
                    </h5>
                </div>
                <div class="card-body">
                    <div id="scanHistory" class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الباركود</th>
                                    <th>الحالة</th>
                                    <th>الوقت</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد عمليات مسح حالياً</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('barcodeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const barcode = document.getElementById('barcode').value;

        // Show success message
        const resultsList = document.getElementById('resultsList');
        const newResult = document.createElement('div');
        newResult.className = 'list-group-item list-group-item-success';
        newResult.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>الباركود:</strong> ${barcode}
                </div>
                <span class="badge badge-success">تم المسح</span>
            </div>
        `;
        resultsList.insertBefore(newResult, resultsList.firstChild);

        document.getElementById('scanResults').style.display = 'block';
        document.getElementById('barcode').value = '';
        document.getElementById('barcode').focus();
    });
</script>
@endsection
