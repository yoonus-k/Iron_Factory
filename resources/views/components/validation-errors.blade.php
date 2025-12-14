{{-- مكون عرض أخطاء التحقق من البيانات --}}
@if($errors->any())
<div class="error-summary-box" role="alert" id="validationErrorsBox">
    <div class="error-summary-header">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <strong>يوجد {{ $errors->count() }} {{ $errors->count() == 1 ? 'خطأ' : 'أخطاء' }} في البيانات المدخلة:</strong>
    </div>
    <ul class="error-list">
        @foreach($errors->all() as $index => $error)
        <li data-error-index="{{ $index }}" title="اضغط للانتقال إلى الحقل">{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="error-close-btn" onclick="this.parentElement.style.display='none'" title="إغلاق">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
</div>

<style>
    /* صندوق عرض الأخطاء */
    .error-summary-box {
        position: relative;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        border-right: 6px solid #dc2626;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        animation: slideDown 0.4s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .error-summary-header {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #991b1b;
        margin-bottom: 14px;
        font-size: 16px;
    }

    .error-summary-header svg {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .error-list {
        margin: 0;
        padding: 0;
        padding-right: 30px;
        list-style: none;
    }

    .error-list li {
        position: relative;
        padding: 10px 0;
        padding-right: 24px;
        color: #7f1d1d;
        font-size: 14px;
        line-height: 1.6;
        border-bottom: 1px solid rgba(239, 68, 68, 0.2);
        cursor: pointer;
        transition: all 0.2s;
    }

    .error-list li:hover {
        background: rgba(239, 68, 68, 0.05);
        padding-right: 28px;
    }

    .error-list li:last-child {
        border-bottom: none;
    }

    .error-list li:before {
        content: "✕";
        position: absolute;
        right: 0;
        top: 10px;
        width: 18px;
        height: 18px;
        background: #dc2626;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
    }

    .error-close-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(220, 38, 38, 0.1);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .error-close-btn:hover {
        background: #dc2626;
        transform: rotate(90deg);
    }

    .error-close-btn svg {
        width: 16px;
        height: 16px;
        stroke: #dc2626;
    }

    .error-close-btn:hover svg {
        stroke: white;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
        font-weight: 500;
        animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .error-message:before {
        content: "⚠";
        font-size: 14px;
    }

    /* تمييز الحقول التي بها أخطاء */
    .is-invalid {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
    }

    .fixing-error {
        animation: pulse 0.5s ease-in-out;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    @media (max-width: 768px) {
        .error-summary-box {
            padding: 16px 20px;
        }

        .error-summary-header {
            font-size: 14px;
        }

        .error-list li {
            font-size: 13px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.getElementById('validationErrorsBox');

        if (errorBox) {
            // التمرير التلقائي لصندوق الأخطاء
            setTimeout(() => {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);

            // إضافة تأثير وميض للفت الانتباه
            let blinkCount = 0;
            const blinkInterval = setInterval(() => {
                errorBox.style.opacity = errorBox.style.opacity === '0.7' ? '1' : '0.7';
                blinkCount++;
                if (blinkCount >= 4) {
                    clearInterval(blinkInterval);
                    errorBox.style.opacity = '1';
                }
            }, 300);

            // معالجة النقر على الأخطاء للانتقال إلى الحقول
            const errorItems = document.querySelectorAll('.error-list li');
            errorItems.forEach((item, index) => {
                item.addEventListener('click', function() {
                    const invalidInputs = document.querySelectorAll('.is-invalid');
                    if (invalidInputs[index]) {
                        invalidInputs[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => {
                            invalidInputs[index].focus();
                            invalidInputs[index].classList.add('fixing-error');
                            setTimeout(() => {
                                invalidInputs[index].classList.remove('fixing-error');
                            }, 500);
                        }, 500);
                    }
                });
            });

            // تحسين التفاعل مع الحقول التي بها أخطاء
            const invalidInputs = document.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const errorMsg = this.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.style.fontWeight = 'bold';
                        errorMsg.style.color = '#991b1b';
                    }
                });

                input.addEventListener('blur', function() {
                    const errorMsg = this.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.style.fontWeight = 'normal';
                        errorMsg.style.color = '#dc2626';
                    }
                });

                // إزالة تنسيق الخطأ عند التعديل
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.style.borderColor = '#10b981';
                        this.style.background = '#f0fdf4';

                        const errorMsg = this.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.style.opacity = '0.5';
                            errorMsg.style.textDecoration = 'line-through';
                        }
                    }
                });
            });
        }
    });
</script>
@endif
