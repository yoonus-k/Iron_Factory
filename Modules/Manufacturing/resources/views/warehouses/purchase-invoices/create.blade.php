@extends('master')

@section('title', 'إضافة فاتورة شراء جديدة')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/invoice-items-table.css') }}">
    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="11" x2="12" y2="17"></line>
                <line x1="9" y1="14" x2="15" y2="14"></line>
            </svg>
            إضافة فاتورة شراء جديدة
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>فواتير الشراء</span>
            <i class="feather icon-chevron-left"></i>
            <span>إضافة فاتورة جديدة</span>
        </nav>
    </div>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="um-alert-custom um-alert-error" role="alert" id="validationErrors">
            <i class="feather icon-alert-circle"></i>
            <strong>خطأ في البيانات:</strong> الرجاء التحقق من المعلومات المدخلة.
            <ul style="margin-top: 10px; margin-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.purchase-invoices.store') }}" id="purchaseInvoiceForm">
            @csrf

            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الفاتورة</h3>
                        <p class="section-subtitle">أدخل بيانات الفاتورة الأساسية</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="invoice_number" class="form-label">
                            رقم الفاتورة (تلقائي)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                <line x1="1" y1="3" x2="23" y2="3"></line>
                                <path d="M10 12v4"></path>
                                <path d="M14 12v4"></path>
                            </svg>
                            <input type="text" name="invoice_number" id="invoice_number"
                                class="form-input" placeholder="سيتم توليده تلقائيًا" value="{{ old('invoice_number', $invoiceNumber) }}" readonly style="background-color: #f1f5f9;" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_date" class="form-label">
                            تاريخ الفاتورة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="invoice_date" id="invoice_date"
                                class="form-input" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label">
                            المورد
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <select name="supplier_id" id="supplier_id" class="form-input" required>
                                <option value="">-- اختر المورد --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_reference_number" class="form-label">رقم مرجع الفاتورة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <line x1="9" y1="11" x2="15" y2="11"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <input type="text" name="invoice_reference_number" id="invoice_reference_number"
                                class="form-input" placeholder="رقم مرجعي من المورد" value="{{ old('invoice_reference_number') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="due_date" id="due_date" class="form-input" value="{{ old('due_date') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 2v4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-2V2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">منتجات الفاتورة</h3>
                        <p class="section-subtitle">أضف المنتجات والمواد الخاصة بالفاتورة</p>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <button type="button" class="btn-submit" id="add-item-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        إضافة منتج
                    </button>
                </div>

                <!-- Items Table -->
                <div style="overflow-x: auto;">
                    <table class="invoice-items-table" id="items-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th style="min-width: 150px;">المادة</th>

                                <th style="min-width: 120px;">الوصف</th>
                                <th style="width: 100px;">الكمية</th>
                                <th style="width: 100px;">الوحدة</th>
                                <th style="width: 100px;">السعر</th>
                                <th style="width: 80px;">ض%</th>
                                <th style="width: 80px;">خ%</th>
                                <th style="width: 80px;">الوزن</th>
                                <th style="width: 100px;">الإجمالي</th>
                                <th style="width: 60px;">إجراء</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-items-container">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Total Summary -->
                <div style="margin-top: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <label style="font-weight: 600; color: #64748b; display: block; margin-bottom: 5px;">الإجمالي الفرعي</label>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;" id="subtotal-display">0.00</div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #64748b; display: block; margin-bottom: 5px;">الضريبة</label>
                            <div style="font-size: 24px; font-weight: 700; color: #f59e0b;" id="tax-display">0.00</div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #64748b; display: block; margin-bottom: 5px;">الخصم</label>
                            <div style="font-size: 24px; font-weight: 700; color: #ef4444;" id="discount-display">0.00</div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #64748b; display: block; margin-bottom: 5px;">الوزن الإجمالي</label>
                            <div style="font-size: 24px; font-weight: 700; color: #8b5cf6;" id="weight-display">0.00 كجم</div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #64748b; display: block; margin-bottom: 5px;">الإجمالي النهائي</label>
                            <div style="font-size: 24px; font-weight: 700; color: #10b981;" id="total-display">0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency and Payment Terms Section -->


            <!-- Status and Activity Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">الحالة والنشاط</h3>
                        <p class="section-subtitle">حدد حالة الفاتورة ونشاطها</p>
                    </div>
                </div>



                    <div class="form-group">
                        <label class="form-label">النشاط</label>
                        <div class="input-wrapper">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    ✓ الفاتورة نشطة
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">ملاحظات إضافية</h3>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input" rows="4" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ الفاتورة
                </button>
                <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="{{ asset('Modules/Manufacturing/resources/views/warehouses/purchase-invoices/styles/invoice-items-table.css') }}">

    <script>
        const materials = @json($materials);
        const units = @json($units);
        let itemCounter = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('purchaseInvoiceForm');
            const inputs = form.querySelectorAll('.form-input');
            const addItemBtn = document.getElementById('add-item-btn');
            const itemsContainer = document.getElementById('invoice-items-container');

            // Add first item automatically
            addItem();

            // Add item button click
            addItemBtn.addEventListener('click', function() {
                addItem();
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                const items = document.querySelectorAll('.invoice-item');
                if (items.length === 0) {
                    e.preventDefault();
                    alert('يجب إضافة منتج واحد على الأقل');
                    return false;
                }
            });

            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                const successMsg = document.getElementById('successMessage');
                const errorMsg = document.getElementById('errorMessage');
                const validationErrors = document.getElementById('validationErrors');

                if (successMsg) {
                    successMsg.style.transition = 'opacity 0.5s';
                    successMsg.style.opacity = '0';
                    setTimeout(() => successMsg.style.display = 'none', 500);
                }
                if (errorMsg) {
                    errorMsg.style.transition = 'opacity 0.5s';
                    errorMsg.style.opacity = '0';
                    setTimeout(() => errorMsg.style.display = 'none', 500);
                }
                if (validationErrors) {
                    validationErrors.style.transition = 'opacity 0.5s';
                    validationErrors.style.opacity = '0';
                    setTimeout(() => validationErrors.style.display = 'none', 500);
                }
            }, 5000);
        });

        function addItem() {
            const container = document.getElementById('invoice-items-container');
            const itemIndex = itemCounter++;
            const rowNumber = itemCounter;

            const itemHtml = `
                <tr class="invoice-item" data-index="${itemIndex}">
                    <td class="row-number">${rowNumber}</td>
                    <td>
                        <select name="items[${itemIndex}][material_id]" class="material-select" onchange="selectMaterial(${itemIndex}, this.value)">
                            <option value="">-- اختر --</option>
                            ${materials.map(m => `<option value="${m.id}" data-name="${m.name_ar}" data-unit-id="${m.unit_id || ''}">${m.name_ar}</option>`).join('')}
                        </select>
                    </td>

                    <td>
                        <textarea name="items[${itemIndex}][description]" rows="1" placeholder="الوصف"></textarea>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][quantity]" class="item-quantity" placeholder="0" step="0.001" min="0.001" value="1" required oninput="calculateItemTotal(${itemIndex})">
                    </td>
                    <td>
                        <select name="items[${itemIndex}][unit]" class="item-unit" required>
                            <option value="">-- اختر --</option>
                            ${units.map(u => `<option value="${u.unit_name}">${u.unit_name} (${u.unit_symbol})</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][unit_price]" class="item-price" placeholder="0.00" step="0.01" min="0" value="0" required oninput="calculateItemTotal(${itemIndex})">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][tax_rate]" class="item-tax-rate" placeholder="0" step="0.01" min="0" max="100" value="0" oninput="calculateItemTotal(${itemIndex})">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][discount_rate]" class="item-discount-rate" placeholder="0" step="0.01" min="0" max="100" value="0" oninput="calculateItemTotal(${itemIndex})">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][weight]" class="item-weight" placeholder="0.00" step="0.01" min="0" value="0" oninput="calculateItemTotal(${itemIndex})">
                    </td>
                    <td>
                        <input type="text" class="item-total" readonly value="0.00">
                    </td>
                    <td>
                        <button type="button" class="remove-item-btn" onclick="removeItem(${itemIndex})">حذف</button>
                    </td>
                    <input type="hidden" name="items[${itemIndex}][notes]" value="">
                </tr>
            `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            calculateGrandTotal();
        }

        function removeItem(index) {
            const item = document.querySelector(`.invoice-item[data-index="${index}"]`);
            if (item) {
                item.remove();
                calculateGrandTotal();
                // Renumber remaining items
                document.querySelectorAll('.invoice-item').forEach((el, idx) => {
                    el.querySelector('.row-number').textContent = idx + 1;
                });
            }
        }

        function selectMaterial(index, materialId) {
            if (!materialId) return;

            const material = materials.find(m => m.id == materialId);
            if (material) {
                const item = document.querySelector(`.invoice-item[data-index="${index}"]`);
                item.querySelector('.item-name').value = material.name;

                // Set unit based on material's unit_id
                if (material.unit_id) {
                    const unit = units.find(u => u.id == material.unit_id);
                    if (unit) {
                        const unitSelect = item.querySelector('.item-unit');
                        unitSelect.value = unit.unit_name;
                    }
                }
            }
        }

        function calculateItemTotal(index) {
            const item = document.querySelector(`.invoice-item[data-index="${index}"]`);
            const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(item.querySelector('.item-price').value) || 0;
            const taxRate = parseFloat(item.querySelector('.item-tax-rate').value) || 0;
            const discountRate = parseFloat(item.querySelector('.item-discount-rate').value) || 0;

            const subtotal = quantity * price;
            const taxAmount = subtotal * (taxRate / 100);
            const discountAmount = subtotal * (discountRate / 100);
            const total = subtotal + taxAmount - discountAmount;

            item.querySelector('.item-total').value = total.toFixed(2);
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let subtotal = 0;
            let taxTotal = 0;
            let discountTotal = 0;
            let grandTotal = 0;
            let totalQuantity = 0;

            document.querySelectorAll('.invoice-item').forEach(item => {
                const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
                const price = parseFloat(item.querySelector('.item-price').value) || 0;
                const taxRate = parseFloat(item.querySelector('.item-tax-rate').value) || 0;
                const discountRate = parseFloat(item.querySelector('.item-discount-rate').value) || 0;

                const itemSubtotal = quantity * price;
                const itemTax = itemSubtotal * (taxRate / 100);
                const itemDiscount = itemSubtotal * (discountRate / 100);
                const itemTotal = itemSubtotal + itemTax - itemDiscount;

                subtotal += itemSubtotal;
                taxTotal += itemTax;
                discountTotal += itemDiscount;
                grandTotal += itemTotal;
                totalQuantity += quantity;
            });

            document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);
            document.getElementById('tax-display').textContent = taxTotal.toFixed(2);
            document.getElementById('discount-display').textContent = discountTotal.toFixed(2);
            document.getElementById('weight-display').textContent = totalQuantity.toFixed(2) + ' كجم';
            document.getElementById('total-display').textContent = grandTotal.toFixed(2);
        }
    </script>

@endsection
