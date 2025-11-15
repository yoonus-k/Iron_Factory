@extends('master')

@section('title', 'تفاصيل المورد')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-truck"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $supplier->getName() }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                {{ $supplier->contact_person }}
                            </span>
                            @if($supplier->is_active)
                                <span class="badge active">نشط</span>
                            @else
                                <span class="badge inactive">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.suppliers.edit', $supplier->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.suppliers.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المورد:</div>
                        <div class="info-value">{{ $supplier->getName() }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الشخص المسؤول:</div>
                        <div class="info-value">{{ $supplier->contact_person }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم الهاتف:</div>
                        <div class="info-value">{{ $supplier->phone }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">{{ $supplier->email }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">العنوان:</div>
                        <div class="info-value">{{ $supplier->address ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المدينة:</div>
                        <div class="info-value">{{ $supplier->city ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @if($supplier->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v4"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">الملاحظات</h3>
                </div>
                <div class="card-body">
                    <p class="info-text">{{ $supplier->notes ?? 'لا توجد ملاحظات' }}</p>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">الفواتير الأخيرة</h3>
                </div>
                <div class="card-body">
                    <div class="um-table-responsive">
                        <table class="um-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد فواتير</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">الإجراءات المتاحة</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    <button type="button" class="action-btn contact" onclick="window.location='tel:{{ $supplier->phone }}'">
                        <div class="action-icon">
                            <i class="feather icon-phone"></i>
                        </div>
                        <div class="action-text">
                            <span>اتصل</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn email" onclick="window.location='mailto:{{ $supplier->email }}'">
                        <div class="action-icon">
                            <i class="feather icon-mail"></i>
                        </div>
                        <div class="action-text">
                            <span>إرسال بريد</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn delete" onclick="deleteSupplier({{ $supplier->id }})">
                        <div class="action-icon">
                            <i class="feather icon-trash-2"></i>
                        </div>
                        <div class="action-text">
                            <span>حذف</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector('.action-btn.delete');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ هل أنت متأكد من حذف هذا المورد؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        // Create a form dynamically
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ url('manufacturing/suppliers') }}/' + {{ $supplier->id }};
                        
                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        // Add method spoofing for DELETE
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);
                        
                        // Submit the form
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });

        function deleteSupplier(id) {
            if (confirm('⚠️ هل أنت متأكد من حذف هذا المورد؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                // Create a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/suppliers') }}/' + id;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method spoofing for DELETE
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection