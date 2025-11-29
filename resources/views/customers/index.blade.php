@extends('master')

@section('title', 'إدارة العملاء')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>
                إدارة العملاء
            </h2>
        </div>
        <div class="col-md-6 text-end">
            @if(Auth::user()->hasPermission('CUSTOMERS_CREATE'))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                <i class="bi bi-plus-circle me-1"></i>
                إضافة عميل جديد
            </button>
            @endif
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">البحث</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="اسم العميل، الشركة، الهاتف، أو رمز العميل">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select class="form-select" name="status">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        بحث
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <div class="card-body">
            @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رمز العميل</th>
                            <th>الاسم</th>
                            <th>الشركة</th>
                            <th>الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>المدينة</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td><strong>{{ $customer->customer_code }}</strong></td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->company_name ?? '-' }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            <td>{{ $customer->city ?? '-' }}</td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(Auth::user()->hasPermission('CUSTOMERS_UPDATE'))
                                    <button type="button" class="btn btn-sm btn-primary edit-customer" 
                                            data-id="{{ $customer->id }}"
                                            data-customer="{{ json_encode($customer) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif

                                    @if(Auth::user()->hasPermission('CUSTOMERS_ACTIVATE'))
                                        @if($customer->is_active)
                                        <button type="button" class="btn btn-sm btn-warning deactivate-customer" 
                                                data-id="{{ $customer->id }}">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-success activate-customer" 
                                                data-id="{{ $customer->id }}">
                                            <i class="bi bi-play-circle"></i>
                                        </button>
                                        @endif
                                    @endif

                                    @if(Auth::user()->hasPermission('CUSTOMERS_DELETE'))
                                    <button type="button" class="btn btn-sm btn-danger delete-customer" 
                                            data-id="{{ $customer->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $customers->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                لا توجد عملاء مسجلين
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Customer Modal -->
@include('customers.partials.customer-modal')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // فتح Modal للإضافة
    $('#customerModal').on('show.bs.modal', function (e) {
        if (!$(e.relatedTarget).hasClass('edit-customer')) {
            // إعادة تعيين النموذج للإضافة
            $('#customerForm')[0].reset();
            $('#customerId').val('');
            $('#customerModalLabel').text('إضافة عميل جديد');
            $('#customerForm').attr('action', '{{ route("customers.store") }}');
            $('#customerForm').find('input[name="_method"]').remove();
        }
    });

    // فتح Modal للتعديل
    $(document).on('click', '.edit-customer', function() {
        const customer = $(this).data('customer');
        
        $('#customerId').val(customer.id);
        $('#customerModalLabel').text('تعديل بيانات العميل');
        
        // ملء الحقول
        $('#name').val(customer.name);
        $('#company_name').val(customer.company_name);
        $('#phone').val(customer.phone);
        $('#email').val(customer.email);
        $('#address').val(customer.address);
        $('#city').val(customer.city);
        $('#country').val(customer.country);
        $('#tax_number').val(customer.tax_number);
        $('#notes').val(customer.notes);
        
        // تحديث رابط النموذج
        $('#customerForm').attr('action', `/customers/${customer.id}`);
        
        // إضافة method PUT
        if ($('#customerForm').find('input[name="_method"]').length === 0) {
            $('#customerForm').append('<input type="hidden" name="_method" value="PUT">');
        }
        
        $('#customerModal').modal('show');
    });

    // حفظ العميل
    $('#customerForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'نجح!',
                        text: response.message,
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الحفظ';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: errorMessage
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // تفعيل عميل
    $(document).on('click', '.activate-customer', function() {
        const customerId = $(this).data('id');
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم تفعيل هذا العميل',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، تفعيل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customers/${customerId}/activate`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('نجح!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل تفعيل العميل', 'error');
                    }
                });
            }
        });
    });

    // تعطيل عميل
    $(document).on('click', '.deactivate-customer', function() {
        const customerId = $(this).data('id');
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم تعطيل هذا العميل',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، تعطيل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customers/${customerId}/deactivate`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('نجح!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل تعطيل العميل', 'error');
                    }
                });
            }
        });
    });

    // حذف عميل
    $(document).on('click', '.delete-customer', function() {
        const customerId = $(this).data('id');
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من التراجع عن هذا الإجراء!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'نعم، حذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customers/${customerId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('تم الحذف!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل حذف العميل', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
