@extends('master')

@section('title', 'طلب إدخال صناديق للمستودع')

@section('content')
<style>
    .intake-header {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
    }
    .intake-header h2 {
        margin: 0;
    }
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: sticky;
        top: 20px;
    }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
    }
    .summary-item {
        background: rgba(255,255,255,0.17);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    .summary-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }
    .boxes-table-wrapper {
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        background: #f5f7fa;
        border-bottom: 2px solid #dee2e6;
        padding: 14px 12px;
        font-weight: 600;
        font-size: 0.95rem;
        color: #2c3e50;
    }
    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .table tbody tr:hover {
        background: linear-gradient(to right, #f8f9ff, #ffffff);
        transform: translateX(-2px);
        box-shadow: 2px 0 8px rgba(102, 126, 234, 0.1);
    }
    .table tbody tr:last-child {
        border-bottom: none;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 16px 12px;
        color: #495057;
    }
    .table tbody td strong {
        color: #2c3e50;
        font-size: 1.05rem;
    }
    .btn-danger {
        transition: all 0.2s ease;
    }
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
    .empty-state {
        padding: 40px;
        text-align: center;
        color: #90a4ae;
    }
</style>

<div class="container-fluid">
    <div class="intake-header mb-4">
        <div>
            <h2>
                <i class="bi bi-box-arrow-in-down text-success me-2"></i>
                طلب إدخال صناديق للمستودع
            </h2>
            <p class="text-muted mb-0">جميع الصناديق الجاهزة من المرحلة الرابعة تظهر مباشرةً، ما عليك سوى مراجعتها ثم إرسال الطلب.</p>
        </div>
        <a href="{{ route('manufacturing.warehouse-intake.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            العودة
        </a>
    </div>

    @if($availableBoxes->isEmpty())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle fs-4 me-2"></i>
            <div>
                لا توجد صناديق جاهزة في المرحلة الرابعة حالياً. حالما يتم تجهيز صناديق جديدة ستظهر هنا تلقائياً.
            </div>
        </div>
    @else
    <form id="intakeForm" method="POST" action="{{ route('manufacturing.warehouse-intake.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 order-2 order-lg-1">
                <div class="boxes-table-wrapper">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                        <div>
                            <strong style="font-size: 1.1rem; color: #2c3e50;">تفاصيل الصناديق المنتجة حديثاً</strong>
                            <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">يتم عرض جميع الصناديق الجاهزة، ويمكن إزالة أي صندوق من الطلب باستخدام زر الإزالة.</p>
                        </div>
                        <span class="badge bg-success px-3 py-2" id="readyCount" style="font-size: 0.95rem;">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $availableBoxes->count() }} جاهز
                        </span>
                    </div>
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table align-middle mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th>الباركود</th>
                                    <th>نوع التغليف</th>
                                    <th>الوزن (كجم)</th>
                                    <th>عدد اللفات</th>
                                    <th>المسؤول</th>
                                    <th>تاريخ الإنتاج</th>
                                    <th class="text-end">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody id="boxesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 order-1 order-lg-2">
                <div class="summary-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1"><i class="bi bi-clipboard-check me-1"></i> ملخص الطلب</h5>
                            <p class="mb-0" style="opacity:0.8; font-size: 0.9rem;">يتم تحديد جميع الصناديق تلقائياً، ويمكنك إزالة أي صندوق قبل الإرسال.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-light" id="resetSelection" style="color:#2e7d32; font-weight: 600;">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>

                    <div class="summary-grid mb-4">
                        <div class="summary-item">
                            <span class="summary-number" id="selectedCount">0</span>
                            <small>إجمالي الصناديق</small>
                        </div>
                        <div class="summary-item">
                            <span class="summary-number" id="totalWeight">0</span>
                            <small>الوزن الإجمالي (كجم)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">ملاحظات (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="أضف أي ملاحظات بخصوص هذا الطلب..."></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-light w-100 py-3 fw-bold" style="font-size: 1.1rem;">
                        <i class="bi bi-send-fill me-2"></i>
                        إرسال طلب الإدخال الآن
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

@endsection

@push('scripts')
@if(!$availableBoxes->isEmpty())
<script>
    const allBoxes = @json($availableBoxes);
    let selectedBoxes = [...allBoxes];

    const tableBody = document.getElementById('boxesTableBody');
    const selectedCountEl = document.getElementById('selectedCount');
    const totalWeightEl = document.getElementById('totalWeight');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetSelection');

    function formatWeight(weight) {
        return (parseFloat(weight || 0).toFixed(2));
    }

    function renderBoxes() {
        if (!tableBody) return;

        if (selectedBoxes.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mb-0">لا توجد صناديق محددة حالياً. يمكنك إعادة تحديد الكل من الزر الموجود في الملخص.</p>
                        </div>
                    </td>
                </tr>`;
            selectedCountEl.textContent = '0';
            totalWeightEl.textContent = '0.00';
            submitBtn.disabled = true;
            return;
        }

        const rows = selectedBoxes.map(box => `
            <tr>
                <td><strong>${box.barcode}</strong></td>
                <td>${box.packaging_type || '-'}</td>
                <td>${formatWeight(box.weight)}</td>
                <td>${box.coils_count || 0}</td>
                <td>${box.creator || 'غير محدد'}</td>
                <td>${box.created_at || '—'}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger" data-remove-box="${box.id}" title="إزالة الصندوق">
                        <i class="bi bi-trash-fill me-1"></i>
                        إزالة
                    </button>
                </td>
            </tr>
        `).join('');

        tableBody.innerHTML = rows;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);
        selectedCountEl.textContent = selectedBoxes.length;
        totalWeightEl.textContent = formatWeight(totalWeight);
        submitBtn.disabled = false;
    }

    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-remove-box]');
        if (!target) return;
        const boxId = parseInt(target.getAttribute('data-remove-box'));
        selectedBoxes = selectedBoxes.filter(box => box.id !== boxId);
        renderBoxes();
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            selectedBoxes = [...allBoxes];
            renderBoxes();
        });
    }

    renderBoxes();

    document.getElementById('intakeForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (selectedBoxes.length === 0) {
            Swal.fire('تنبيه', 'لا توجد صناديق لإرسالها', 'warning');
            return;
        }

        const formData = new FormData(this);
        selectedBoxes.forEach((box, index) => {
            formData.append(`boxes[${index}][box_id]`, box.id);
            formData.append(`boxes[${index}][barcode]`, box.barcode);
        });

        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i> جاري الإرسال...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                const validation = data?.errors ? Object.values(data.errors).flat().join(' ') : null;
                const message = validation || data?.error || 'فشل إنشاء الطلب';
                throw new Error(message);
            }
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'تم إنشاء الطلب بنجاح',
                html: `<p>${data.message}</p><p class="mb-0"><strong>رقم الطلب:</strong> ${data.request_number}</p>`,
                confirmButtonText: 'عرض الطلب'
            }).then(() => {
                window.location.href = `/warehouse-intake/${data.request_id}`;
            });
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'خطأ', text: error.message });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
</script>
@endif
@endpush
