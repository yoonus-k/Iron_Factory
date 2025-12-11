<div class="confirmation-card" id="confirmation-{{ $confirmation->id }}">
    <div class="confirmation-header">
        <div class="confirmation-title">
            <i class="fas fa-box-open"></i>
            <h4>
                @php
                    $materialName = $confirmation->deliveryNote?->material?->name_ar 
                        ?? $confirmation->deliveryNote?->material?->name 
                        ?? $confirmation->batch?->material?->name_ar 
                        ?? $confirmation->batch?->material?->name 
                        ?? 'غير محدد';
                @endphp
                {{ $materialName }}
            </h4>
        </div>
        <div class="confirmation-time">
            <i class="fas fa-clock"></i>
            {{ $confirmation->created_at->diffForHumans() }}
        </div>
    </div>
    
    <div class="confirmation-body">
        <div class="confirmation-details">
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-barcode"></i> باركود الإنتاج
                </span>
                <span class="detail-value">
                    @php
                        $barcode = $confirmation->deliveryNote?->production_barcode 
                            ?? $confirmation->deliveryNote?->materialBatch?->batch_code 
                            ?? $confirmation->batch?->batch_code 
                            ?? 'غير محدد';
                    @endphp
                    {{ $barcode }}
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-weight"></i> الوزن النهائي
                </span>
                <span class="detail-value">
                    @php
                        $weight = $confirmation->actual_received_quantity;

                        // أولاً: نعرض الكمية المستلمة فعلياً إن وجدت
                        if (is_null($weight) || $weight <= 0) {
                            // ثانياً: نحاول أخذ الكمية المنقولة من DeliveryNote نفسه
                            if ($confirmation->deliveryNote && isset($confirmation->deliveryNote->quantity_used)) {
                                $lastTransferQuantity = $confirmation->deliveryNote->quantity_used;
                                if ($lastTransferQuantity > 0) {
                                    $weight = $lastTransferQuantity;
                                }
                            }
                        }

                        // ثالثاً: نحاول أخذ الوزن من MaterialBatch
                        if ((is_null($weight) || $weight <= 0) && $confirmation->deliveryNote?->materialBatch) {
                            $weight = $confirmation->deliveryNote->materialBatch->initial_quantity 
                                ?? $confirmation->deliveryNote->materialBatch->available_quantity;
                        }

                        // رابعاً: نحاول أخذ الوزن من Batch المرتبط بالتأكيد
                        if ((is_null($weight) || $weight <= 0) && $confirmation->batch) {
                            $weight = $confirmation->batch->initial_quantity 
                                ?? $confirmation->batch->available_quantity;
                        }

                        // خامساً: نحاول أخذ الكمية من DeliveryNote كحل أخير
                        if ((is_null($weight) || $weight <= 0) && $confirmation->deliveryNote) {
                            $weight = $confirmation->deliveryNote->quantity 
                                ?? $confirmation->deliveryNote->actual_weight;
                        }

                        $displayWeight = $weight ? number_format($weight, 2) : 'غير محدد';
                    @endphp
                    {{ $displayWeight }} @if($weight) كجم @endif
                </span>
            </div>
        </div>
        
        @if($confirmation->notes)
            <div class="confirmation-notes">
                <i class="fas fa-sticky-note"></i>
                <span>{{ $confirmation->notes }}</span>
            </div>
        @endif
    </div>

    <div class="confirmation-actions">
        <button class="action-btn btn-confirm quick-confirm" 
                data-id="{{ $confirmation->id }}"
                title="تأكيد الاستلام">
            <i class="fas fa-check"></i>
            <span>تأكيد</span>
        </button>
        <button class="action-btn btn-reject quick-reject" 
                data-id="{{ $confirmation->id }}"
                title="رفض الاستلام">
            <i class="fas fa-times"></i>
            <span>رفض</span>
        </button>
        <a href="{{ route('manufacturing.production.confirmations.show', $confirmation->id) }}" 
           class="action-btn btn-details"
           title="عرض التفاصيل">
            <i class="fas fa-eye"></i>
            <span>تفاصيل</span>
        </a>
    </div>
</div>

<style>
    .confirmation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e9ecef;
    }

    .confirmation-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .confirmation-title i {
        font-size: 20px;
        color: #007bff;
    }

    .confirmation-title h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .confirmation-time {
        font-size: 12px;
        color: #999;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .confirmation-body {
        margin-bottom: 15px;
    }

    .confirmation-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .detail-label {
        font-size: 12px;
        color: #666;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .detail-label i {
        color: #999;
    }

    .detail-value {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 600;
    }

    .confirmation-notes {
        background: #fff3cd;
        border-left: 3px solid #ffc107;
        padding: 10px 12px;
        border-radius: 4px;
        font-size: 13px;
        color: #856404;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .confirmation-notes i {
        color: #ffc107;
    }

    .confirmation-actions {
        display: flex;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
    }

    .action-btn {
        flex: 1;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-confirm {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
    }

    .btn-confirm:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40,167,69,0.3);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%);
        color: white;
    }

    .btn-reject:hover {
        background: linear-gradient(135deg, #bd2130 0%, #a71d2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220,53,69,0.3);
    }

    .btn-details {
        background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
        color: white;
    }

    .btn-details:hover {
        background: linear-gradient(135deg, #117a8b 0%, #0c5460 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23,162,184,0.3);
        color: white;
    }
</style>
