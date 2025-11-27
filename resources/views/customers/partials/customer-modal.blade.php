<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerModalLabel">إضافة عميل جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
                @csrf
                <input type="hidden" id="customerId" name="id">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- الاسم -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                اسم العميل <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <!-- اسم الشركة -->
                        <div class="col-md-6">
                            <label for="company_name" class="form-label">اسم الشركة</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                        </div>

                        <!-- الهاتف -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                رقم الهاتف <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>

                        <!-- العنوان -->
                        <div class="col-12">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>

                        <!-- المدينة -->
                        <div class="col-md-4">
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>

                        <!-- الدولة -->
                        <div class="col-md-4">
                            <label for="country" class="form-label">الدولة</label>
                            <input type="text" class="form-control" id="country" name="country">
                        </div>

                        <!-- الرقم الضريبي -->
                        <div class="col-md-4">
                            <label for="tax_number" class="form-label">الرقم الضريبي</label>
                            <input type="text" class="form-control" id="tax_number" name="tax_number">
                        </div>

                        <!-- ملاحظات -->
                        <div class="col-12">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
