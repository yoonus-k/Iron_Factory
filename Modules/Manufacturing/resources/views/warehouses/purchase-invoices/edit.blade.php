@extends('master')

@section('title', 'تعديل فاتورة شراء')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="um-alert-custom um-alert-error" role="alert">
            <i class="feather icon-alert-circle"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-edit"></i>
                    </div>
                    <div class="header-info">
                        <h1>تعديل فاتورة #{{ $invoice->invoice_number }}</h1>
                        <p>تحديث معلومات الفاتورة</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('manufacturing.purchase-invoices.update', $invoice->id) }}" class="grid">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الفاتورة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الفاتورة *</div>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                               name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                        @error('invoice_number')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم مرجع الفاتورة</div>
                        <input type="text" class="form-control" name="invoice_reference_number"
                               value="{{ old('invoice_reference_number', $invoice->invoice_reference_number) }}"
                               placeholder="رقم مرجعي من المورد">
                    </div>

                    <div class="info-item">
                        <div class="info-label">المورد *</div>
                        <select class="form-control @error('supplier_id') is-invalid @enderror"
                                name="supplier_id" required>
                            <option value="">-- اختر مورد --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $invoice->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الفاتورة *</div>
                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                               name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الاستحقاق</div>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                               name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
                        @error('due_date')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">المبالغ والعملة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي *</div>
                        <div class="input-group">
                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                   name="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}"
                                   step="0.01" required placeholder="0.00" id="total_amount">
                            <select class="form-select" name="currency" style="max-width: 100px;">
                                <option value="SAR" {{ old('currency', $invoice->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                                <option value="USD" {{ old('currency', $invoice->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency', $invoice->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="AED" {{ old('currency', $invoice->currency) == 'AED' ? 'selected' : '' }}>AED</option>
                            </select>
                        </div>
                        @error('total_amount')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">شروط الدفع</div>
                        <select class="form-control" name="payment_terms">
                            <option value="">-- اختر شروط الدفع --</option>
                            <option value="دفع فوري" {{ old('payment_terms', $invoice->payment_terms) == 'دفع فوري' ? 'selected' : '' }}>دفع فوري</option>
                            <option value="15 يوم" {{ old('payment_terms', $invoice->payment_terms) == '15 يوم' ? 'selected' : '' }}>15 يوم</option>
                            <option value="30 يوم" {{ old('payment_terms', $invoice->payment_terms) == '30 يوم' ? 'selected' : '' }}>30 يوم</option>
                            <option value="45 يوم" {{ old('payment_terms', $invoice->payment_terms) == '45 يوم' ? 'selected' : '' }}>45 يوم</option>
                            <option value="60 يوم" {{ old('payment_terms', $invoice->payment_terms) == '60 يوم' ? 'selected' : '' }}>60 يوم</option>
                            <option value="مخصص" {{ old('payment_terms', $invoice->payment_terms) == 'مخصص' ? 'selected' : '' }}>مخصص</option>
                        </select>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة *</div>
                        <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                            <option value="">-- اختر الحالة --</option>
                            <option value="draft" {{ old('status', $invoice->status->value) == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="pending" {{ old('status', $invoice->status->value) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ old('status', $invoice->status->value) == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                            <option value="paid" {{ old('status', $invoice->status->value) == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                        </select>
                        @error('status')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">النشاط</div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', $invoice->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                ✓ الفاتورة نشطة
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">ملاحظات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">ملاحظات</div>
                        <textarea class="form-control" name="notes" rows="4"
                                  placeholder="أي ملاحظات إضافية أو معلومات مهمة عن الفاتورة">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">الملخص</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي:</div>
                        <div class="info-value">
                            <strong id="total_display" style="font-size: 1.3em; color: #2c3e50;">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</strong>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="btn btn-secondary" style="width: 100%;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('total_amount')?.addEventListener('input', function() {
            const currency = document.querySelector('select[name="currency"]').value;
            const amount = parseFloat(this.value || 0).toFixed(2);
            document.getElementById('total_display').textContent = amount + ' ' + currency;
        });

        document.querySelector('select[name="currency"]')?.addEventListener('change', function() {
            const amount = parseFloat(document.getElementById('total_amount').value || 0).toFixed(2);
            document.getElementById('total_display').textContent = amount + ' ' + this.value;
        });
    </script>
@endsection
