@extends('master')

@section('title', 'إنهاء المعالجة - المرحلة الثانية')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-check-circle"></i> إنهاء المعالجة
            </h1>
            <p class="text-muted">قم بإنهاء العمليات المعالجة والتحقق من جودة المخرجات</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks"></i> قائمة المعالجات المعلقة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم المعالجة</th>
                                    <th>الاستاند المصدر</th>
                                    <th>الحالة</th>
                                    <th>الوزن المعالج</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>#P001</strong></td>
                                    <td>استاند #S001</td>
                                    <td><span class="badge badge-info">قيد المعالجة</span></td>
                                    <td>96.5 كغ</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="completeProcess(1)">
                                            <i class="fas fa-check"></i> إنهاء
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#P002</strong></td>
                                    <td>استاند #S002</td>
                                    <td><span class="badge badge-info">قيد المعالجة</span></td>
                                    <td>97.2 كغ</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="completeProcess(2)">
                                            <i class="fas fa-check"></i> إنهاء
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#P003</strong></td>
                                    <td>استاند #S003</td>
                                    <td><span class="badge badge-info">قيد المعالجة</span></td>
                                    <td>95.8 كغ</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="completeProcess(3)">
                                            <i class="fas fa-check"></i> إنهاء
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form to complete processing -->
            <div class="card mt-4" id="completeForm" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> نموذج إنهاء المعالجة
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manufacturing.stage2.complete') }}" id="processForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="processId" class="form-label">رقم المعالجة:</label>
                            <input type="text" id="processId" class="form-control" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label for="outputWeight" class="form-label">وزن المخرجات النهائي:</label>
                            <input
                                type="number"
                                id="outputWeight"
                                name="output_weight"
                                class="form-control"
                                step="0.01"
                                required
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label for="wasteWeight" class="form-label">وزن الهدر:</label>
                            <input
                                type="number"
                                id="wasteWeight"
                                name="waste_weight"
                                class="form-control"
                                step="0.01"
                                required
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label for="qualityStatus" class="form-label">حالة الجودة:</label>
                            <select id="qualityStatus" name="quality_status" class="form-control" required>
                                <option value="">-- اختر حالة الجودة --</option>
                                <option value="excellent">ممتاز</option>
                                <option value="good">جيد</option>
                                <option value="acceptable">مقبول</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">ملاحظات:</label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control"
                                rows="3"
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> تأكيد إنهاء المعالجة
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg" onclick="cancelForm()">
                                <i class="fas fa-times"></i> إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function completeProcess(processId) {
        document.getElementById('completeForm').style.display = 'block';
        document.getElementById('processId').value = '#P' + String(processId).padStart(3, '0');
        window.scrollTo(0, document.getElementById('completeForm').offsetTop);
    }

    function cancelForm() {
        document.getElementById('completeForm').style.display = 'none';
    }
</script>
@endsection
