@extends('master')

@section('title', __('warehouse_intake.create_title'))

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
                {{ __('warehouse_intake.intake_request_header') }}
            </h2>
            <p class="text-muted mb-0">{{ __('warehouse_intake.intake_description') }}</p>
        </div>
        <a href="{{ route('manufacturing.warehouse-intake.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            {{ __('warehouse_intake.back') }}
        </a>
    </div>

    @if($availableBoxes->isEmpty())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle fs-4 me-2"></i>
            <div>
                {{ __('warehouse_intake.no_boxes_ready') }}
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
                            <strong style="font-size: 1.1rem; color: #2c3e50;">{{ __('warehouse_intake.recently_produced_boxes') }}</strong>
                            <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">{{ __('warehouse_intake.warehouse_entry_info') }}</p>
                        </div>
                        <span class="badge bg-success px-3 py-2" id="readyCount" style="font-size: 0.95rem;">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $availableBoxes->count() }} {{ __('warehouse_intake.ready') }}
                        </span>
                    </div>
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table align-middle mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th>{{ __('warehouse_intake.barcode') }}</th>
                                    <th>{{ __('warehouse_intake.packaging_type') }}</th>
                                    <th>{{ __('warehouse_intake.specifications') }}</th>
                                    <th>{{ __('warehouse_intake.weight') }}</th>
                                    <th>{{ __('warehouse_intake.responsible') }}</th>
                                    <th>{{ __('warehouse_intake.production_date') }}</th>
                                    <th class="text-end">{{ __('warehouse_intake.action') }}</th>
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
                            <h5 class="mb-1"><i class="bi bi-clipboard-check me-1"></i> {{ __('warehouse_intake.request_summary') }}</h5>
                            <p class="mb-0" style="opacity:0.8; font-size: 0.9rem;">{{ __('warehouse_intake.summary_info') }}</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-light" id="resetSelection" style="color:#2e7d32; font-weight: 600;">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>

                    <div class="summary-grid mb-4">
                        <div class="summary-item">
                            <span class="summary-number" id="selectedCount">0</span>
                            <small>{{ __('warehouse_intake.total_boxes') }}</small>
                        </div>
                        <div class="summary-item">
                            <span class="summary-number" id="totalWeight">0</span>
                            <small>{{ __('warehouse_intake.total_weight') }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">{{ __('warehouse_intake.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="{{ __('warehouse_intake.add_notes') }}"></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-light w-100 py-3 fw-bold" style="font-size: 1.1rem;">
                        <i class="bi bi-send-fill me-2"></i>
                        {{ __('warehouse_intake.request_intake') }}
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
        return parseFloat(weight || 0).toFixed(2);
    }

    function renderBoxes() {
        if (!tableBody) return;

        if (selectedBoxes.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mb-0">{{ __('warehouse_intake.no_boxes_selected_currently') }}</p>
                        </div>
                    </td>
                </tr>`;
            selectedCountEl.textContent = '0';
            totalWeightEl.textContent = '0.00';
            if (submitBtn) submitBtn.disabled = true;
            return;
        }

        const rows = selectedBoxes.map(box => {
            const specsHtml = box.specifications
                ? `<span class="badge bg-info me-1">${box.specifications}</span>`
                : '<span style="color: #999;">-</span>';

            return `
                <tr>
                    <td><strong>${box.barcode}</strong></td>
                    <td>${box.packaging_type || '-'}</td>
                    <td>${specsHtml}</td>
                    <td>${formatWeight(box.weight)}</td>
                    <td>${box.creator || '{{ __('warehouse_intake.not_specified') }}'}</td>
                    <td>${box.created_at || '{{ __('warehouse_intake.not_available') }}'}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-danger" data-remove-box="${box.id}" title="{{ __('warehouse_intake.remove') }}">
                            <i class="bi bi-trash-fill me-1"></i>
                            {{ __('warehouse_intake.remove') }}
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        tableBody.innerHTML = rows;
        const totalWeight = selectedBoxes.reduce((sum, box) => sum + parseFloat(box.weight || 0), 0);
        selectedCountEl.textContent = selectedBoxes.length;
        totalWeightEl.textContent = formatWeight(totalWeight);
        if (submitBtn) submitBtn.disabled = false;
    }

    // إزالة صندوق من الاختيار
    document.addEventListener('click', function (e) {
        const target = e.target.closest('[data-remove-box]');
        if (!target) return;
        const boxId = parseInt(target.getAttribute('data-remove-box'));
        selectedBoxes = selectedBoxes.filter(box => box.id !== boxId);
        renderBoxes();
    });

    // إعادة تعيين الاختيار
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            selectedBoxes = [...allBoxes];
            renderBoxes();
        });
    }

    // العرض الأولي
    renderBoxes();

    const intakeForm = document.getElementById('intakeForm');
    if (intakeForm && submitBtn) {
        intakeForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (selectedBoxes.length === 0) {
                Swal.fire('{{ __('warehouse_intake.alert') }}', '{{ __('warehouse_intake.no_boxes_selected') }}', 'warning');
                return;
            }

            const formData = new FormData(this);
            selectedBoxes.forEach((box, index) => {
                formData.append(`boxes[${index}][box_id]`, box.id);
                formData.append(`boxes[${index}][barcode]`, box.barcode);
            });

            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i> {{ __('warehouse_intake.sending') }}';

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
                        const validation = data && data.errors ? Object.values(data.errors).flat().join(' ') : null;
                        const message = validation || (data && data.error) || '{{ __('warehouse_intake.request_creation_failed') }}';
                        throw new Error(message);
                    }
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('warehouse_intake.request_created_success') }}',
                        html: `<p>${data.message}</p><p class="mb-0"><strong>{{ __('warehouse_intake.request_number') }}:</strong> ${data.request_number}</p>`,
                        confirmButtonText: '{{ __('warehouse_intake.view_request') }}'
                    }).then(() => {
                        window.location.href = `/warehouse-intake/${data.request_id}`;
                    });
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: '{{ __('warehouse_intake.error') }}', text: error.message });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    }
</script>
@endif
@endpush
