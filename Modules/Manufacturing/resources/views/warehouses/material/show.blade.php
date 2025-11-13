@extends('master')

@section('title', 'تفاصيل المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $material->material_type }} ({{ $material->material_type_en }})</h1>
                        <div class="badges">
                            <span class="badge badge-{{ $material->status == 'available' ? 'success' : 'warning' }}">
                                {{ $material->status == 'available' ? 'متوفر' : 'قيد الاستخدام' }}
                            </span>
                            <span class="badge badge-info">{{ $material->getCategoryLabel() }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.warehouse-products.index') }}" class="btn btn-back">
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
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            رمز المادة:
                        </div>
                        <div class="info-value">{{ $material->barcode }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            اسم المادة:
                        </div>
                        <div class="info-value">{{ $material->material_type }}</div>
                    </div>
                    
                    @if($material->material_type_en)
                    <div class="info-item">
                        <div class="info-label">
                            Material Name:
                        </div>
                        <div class="info-value">{{ $material->material_type_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            الوزن الأصلي:
                        </div>
                        <div class="info-value">{{ $material->original_weight }} {{ $material->unit->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            الوزن المتبقي:
                        </div>
                        <div class="info-value">{{ $material->remaining_weight }} {{ $material->unit->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            الحالة:
                        </div>
                        <div class="info-value">
                            @if ($material->status === 'available')
                                <span class="badge badge-success">متوفر</span>
                            @elseif ($material->status === 'in_use')
                                <span class="badge badge-warning">قيد الاستخدام</span>
                            @elseif ($material->status === 'consumed')
                                <span class="badge badge-danger">مستهلك</span>
                            @else
                                <span class="badge badge-secondary">منتهي الصلاحية</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            رقم مذكرة التسليم:
                        </div>
                        <div class="info-value">{{ $material->delivery_note_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            معرف الفاتورة:
                        </div>
                        <div class="info-value">{{ $material->purchaseInvoice->invoice_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            تاريخ الصنع:
                        </div>
                        <div class="info-value">{{ $material->manufacture_date?->format('Y-m-d') ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            تاريخ الصلاحية:
                        </div>
                        <div class="info-value">
                            @if ($material->expiry_date)
                                @if ($material->isExpired())
                                    <span class="badge badge-danger">منتهي</span> {{ $material->expiry_date->format('Y-m-d') }}
                                @elseif ($material->isExpiringSoon())
                                    <span class="badge badge-warning">قريب الانتهاء</span> {{ $material->expiry_date->format('Y-m-d') }}
                                @else
                                    {{ $material->expiry_date->format('Y-m-d') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            موقع التخزين:
                        </div>
                        <div class="info-value">
                            @if($material->shelf_location)
                                {{ $material->shelf_location }}
                            @elseif($material->shelf_location_en)
                                {{ $material->shelf_location_en }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    
                    @if($material->shelf_location && $material->shelf_location_en)
                    <div class="info-item">
                        <div class="info-label">
                            Shelf Location:
                        </div>
                        <div class="info-value">{{ $material->shelf_location_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            رقم الدفعة:
                        </div>
                        <div class="info-value">{{ $material->batch_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            تم الإدخال بواسطة:
                        </div>
                        <div class="info-value">{{ $material->creator->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    @if ($material->supplier)
                        <div class="info-item">
                            <div class="info-label">اسم المورد:</div>
                            <div class="info-value">{{ $material->supplier->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">الشخص المسؤول:</div>
                            <div class="info-value">{{ $material->supplier->contact_person ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">الهاتف:</div>
                            <div class="info-value">{{ $material->supplier->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">البريد الإلكتروني:</div>
                            <div class="info-value">{{ $material->supplier->email ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">العنوان:</div>
                            <div class="info-value">{{ $material->supplier->address ?? 'N/A' }}</div>
                        </div>
                    @else
                        <p class="text-muted">لا توجد معلومات عن المورد</p>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">الملاحظات</h3>
                </div>
                <div class="card-body">
                    @if($material->notes)
                        <p>{{ $material->notes }}</p>
                    @elseif($material->notes_en)
                        <p>{{ $material->notes_en }}</p>
                    @else
                        <p>لا توجد ملاحظات</p>
                    @endif
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
                    <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h5>تعديل المادة</h5>
                            <p>تحديث معلومات المادة</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete" onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟\n\nهذا الإجراء لا يمكن التراجع عنه!')">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h5>حذف المادة</h5>
                                <p>إزالة المادة من النظام</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation with SweetAlert2
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذه المادة؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection