<style>
    /* تنسيق نتائج البحث */
    #delivery_notes_results, #invoices_results {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(0, 81, 229, 0.1);
    }

    .delivery-note-item, .invoice-item {
        border-bottom: 1px solid #e9ecef !important;
        transition: all 0.2s ease;
        padding: 12px 15px !important;
    }

    .delivery-note-item:hover, .invoice-item:hover {
        background-color: #e8f0ff !important;
        border-left: 4px solid #0051E5 !important;
        padding-left: 11px !important;
    }

    .delivery-note-item:last-child, .invoice-item:last-child {
        border-bottom: none !important;
    }

    /* تنسيق الأزرار */
    .btn-info {
        background-color: #0051E5;
        border-color: #0051E5;
        color: white;
    }

    .btn-info:hover {
        background-color: #003FA0;
        border-color: #003FA0;
        color: white;
    }

    .btn-outline-danger {
        border-color: #E74C3C;
        color: #E74C3C;
    }

    .btn-outline-danger:hover {
        background-color: #E74C3C;
        border-color: #E74C3C;
        color: white;
    }

    /* تنسيق بطاقات المعلومات */
    #deliveryNoteInfo, #invoiceInfo {
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* تنسيق صناديق الحساب */
    .text-success {
        color: #27ae60 !important;
    }

    .text-danger {
        color: #E74C3C !important;
    }

    .text-primary {
        color: #0051E5 !important;
    }

    .d-flex.gap-2 {
        gap: 0.5rem;
    }
</style>
@endsection