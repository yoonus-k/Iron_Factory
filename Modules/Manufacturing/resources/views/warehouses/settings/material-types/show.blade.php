@extends('master')

@section('title', 'تفاصيل نوع المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $materialType->type_name }}</h1>
                        @if($materialType->type_name_en)
                            <p class="course-subtitle">{{ $materialType->type_name_en }}</p>
                        @endif
                        <div class="badges">
                            @if ($materialType->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                            @php
                                $categories = [
                                    'raw_material' => 'مادة خام',
                                    'finished_product' => 'منتج نهائي',
                                    'semi_finished' => 'منتج شبه مكتمل',
                                    'additive' => 'مادة مضافة',
                                    'packing_material' => 'مادة تغليف',
                                    'component' => 'مكون'
                                ];
                            @endphp
                            <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn btn-back">
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
            <!-- معلومات النوع الأساسية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات النوع الأساسية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رمز النوع:</div>
                        <div class="info-value">
                            <span class="badge badge-primary">{{ $materialType->type_code }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">اسم النوع (عربي):</div>
                        <div class="info-value">{{ $materialType->type_name }}</div>
                    </div>

                    @if($materialType->type_name_en)
                    <div class="info-item">
                        <div class="info-label">اسم النوع (إنجليزي):</div>
                        <div class="info-value">{{ $materialType->type_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">الفئة:</div>
                        <div class="info-value">
                            <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                        </div>
                    </div>

                    @if($materialType->default_unit)
                    <div class="info-item">
                        <div class="info-label">الوحدة الافتراضية:</div>
                        <div class="info-value">{{ $materialType->unit?->unit_name }} ({{ $materialType->unit?->unit_code }})</div>
                    </div>
                    @endif

                    @if($materialType->standard_cost)
                    <div class="info-item">
                        <div class="info-label">التكلفة القياسية:</div>
                        <div class="info-value">{{ $materialType->standard_cost }}</div>
                    </div>
                    @endif

                    @if($materialType->shelf_life_days)
                    <div class="info-item">
                        <div class="info-label">مدة الصلاحية (أيام):</div>
                        <div class="info-value">{{ $materialType->shelf_life_days }} يوم</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @if ($materialType->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوصف وشروط التخزين -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">الوصف وشروط التخزين</h3>
                </div>
                <div class="card-body">
                    @if($materialType->description)
                    <div class="info-item">
                        <div class="info-label">الوصف (عربي):</div>
                        <div class="info-value">{{ $materialType->description }}</div>
                    </div>
                    @endif

                    @if($materialType->description_en)
                    <div class="info-item">
                        <div class="info-label">الوصف (إنجليزي):</div>
                        <div class="info-value">{{ $materialType->description_en }}</div>
                    </div>
                    @endif

                    @if($materialType->storage_conditions)
                    <div class="info-item">
                        <div class="info-label">شروط التخزين (عربي):</div>
                        <div class="info-value">{{ $materialType->storage_conditions }}</div>
                    </div>
                    @endif

                    @if($materialType->storage_conditions_en)
                    <div class="info-item">
                        <div class="info-label">شروط التخزين (إنجليزي):</div>
                        <div class="info-value">{{ $materialType->storage_conditions_en }}</div>
                    </div>
                    @endif

                    @if(!$materialType->description && !$materialType->description_en && !$materialType->storage_conditions && !$materialType->storage_conditions_en)
                    <p class="text-muted">لا توجد معلومات إضافية</p>
                    @endif
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">أنشئت بواسطة:</div>
                        <div class="info-value">{{ $materialType->creator->name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء:</div>
                        <div class="info-value">{{ $materialType->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    @if($materialType->updated_at != $materialType->created_at)
                    <div class="info-item">
                        <div class="info-label">تاريخ آخر تعديل:</div>
                        <div class="info-value">{{ $materialType->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon danger">
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
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h5>تعديل النوع</h5>
                            <p>تحديث معلومات نوع المادة</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('manufacturing.warehouse-settings.material-types.destroy', $materialType->id) }}" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete" style="width: 100%;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h5>حذف النوع</h5>
                                <p>إزالة نوع المادة من النظام</p>
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
                        text: 'هل أنت متأكد من حذف هذا النوع؟ هذا الإجراء لا يمكن التراجع عنه!',
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
