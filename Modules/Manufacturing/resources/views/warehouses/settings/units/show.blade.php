@extends('master')

@section('title', 'تفاصيل الوحدة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-box"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $unit->unit_name }}</h1>
                        @if($unit->unit_name_en)
                            <p class="course-subtitle">{{ $unit->unit_name_en }}</p>
                        @endif
                        <div class="badges">
                            @if ($unit->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                            @php
                                $types = [
                                    'weight' => 'الوزن',
                                    'length' => 'الطول',
                                    'volume' => 'الحجم',
                                    'area' => 'المساحة',
                                    'quantity' => 'الكمية',
                                    'time' => 'الوقت',
                                    'temperature' => 'درجة الحرارة',
                                    'other' => 'أخرى'
                                ];
                            @endphp
                            <span class="badge badge-info">{{ $types[$unit->unit_type] ?? $unit->unit_type }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.units.edit', $unit->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn btn-back">
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
            <!-- معلومات الوحدة الأساسية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الوحدة الأساسية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رمز الوحدة:</div>
                        <div class="info-value">
                            <span class="badge badge-primary">{{ $unit->unit_code }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">اسم الوحدة (عربي):</div>
                        <div class="info-value">{{ $unit->unit_name }}</div>
                    </div>

                    @if($unit->unit_name_en)
                    <div class="info-item">
                        <div class="info-label">اسم الوحدة (إنجليزي):</div>
                        <div class="info-value">{{ $unit->unit_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">الاختصار:</div>
                        <div class="info-value">
                            <span class="badge badge-info">{{ $unit->unit_symbol }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نوع الوحدة:</div>
                        <div class="info-value">
                            <span class="badge badge-info">{{ $types[$unit->unit_type] ?? $unit->unit_type }}</span>
                        </div>
                    </div>

                    @if($unit->conversion_factor)
                    <div class="info-item">
                        <div class="info-label">معامل التحويل:</div>
                        <div class="info-value">{{ $unit->conversion_factor }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @if ($unit->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
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
                    <h3 class="card-title">الوصف</h3>
                </div>
                <div class="card-body">
                    @if($unit->description)
                    <div class="info-item">
                        <div class="info-label">الوصف (عربي):</div>
                        <div class="info-value">{{ $unit->description }}</div>
                    </div>
                    @endif

                    @if($unit->description_en)
                    <div class="info-item">
                        <div class="info-label">الوصف (إنجليزي):</div>
                        <div class="info-value">{{ $unit->description_en }}</div>
                    </div>
                    @endif

                    @if(!$unit->description && !$unit->description_en)
                    <p class="text-muted">لا يوجد وصف</p>
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
                        <div class="info-value">{{ $unit->creator->name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء:</div>
                        <div class="info-value">{{ $unit->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    @if($unit->updated_at != $unit->created_at)
                    <div class="info-item">
                        <div class="info-label">تاريخ آخر تعديل:</div>
                        <div class="info-value">{{ $unit->updated_at->format('Y-m-d H:i') }}</div>
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
                    <a href="{{ route('manufacturing.warehouse-settings.units.edit', $unit->id) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h5>تعديل الوحدة</h5>
                            <p>تحديث معلومات الوحدة</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('manufacturing.warehouse-settings.units.destroy', $unit->id) }}" style="flex: 1;">
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
                                <h5>حذف الوحدة</h5>
                                <p>إزالة الوحدة من النظام</p>
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
                        text: 'هل أنت متأكد من حذف هذه الوحدة؟ هذا الإجراء لا يمكن التراجع عنه!',
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
