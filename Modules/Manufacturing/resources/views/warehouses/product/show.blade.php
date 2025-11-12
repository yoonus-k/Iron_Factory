@extends('master')

@section('title', 'تفاصيل المادة')

@section('content')
    <link rel="stylesheet" href="assets/css/style-cours.css">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $material->material_type }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                {{ $material->material_type }}
                            </span>
                            @if ($material->status == 'available')
                                <span class="badge active">متوفر</span>
                            @elseif ($material->status == 'in_use')
                                <span class="badge active">قيد الاستخدام</span>
                            @elseif ($material->status == 'consumed')
                                <span class="badge featured">مستهلك</span>
                            @else
                                <span class="badge">{{ $material->status }}</span>
                            @endif
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
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            رمز المادة
                        </div>
                        <div class="info-value">{{ $material->barcode }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            الملاحظات
                        </div>
                        <div class="info-value">{{ $material->notes ?: 'لا توجد ملاحظات' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            الوزن الأصلي
                        </div>
                        <div class="info-value">{{ $material->original_weight }} {{ $material->unit }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            الوزن المتبقي
                        </div>
                        <div class="info-value">{{ $material->remaining_weight }} {{ $material->unit }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            حالة المادة
                        </div>
                        <div class="info-value">
                            @if ($material->status == 'available')
                                متوفر
                            @elseif ($material->status == 'in_use')
                                قيد الاستخدام
                            @elseif ($material->status == 'consumed')
                                مستهلك
                            @elseif ($material->status == 'expired')
                                منتهي الصلاحية
                            @else
                                {{ $material->status }}
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            رقم مذكرة التسليم
                        </div>
                        <div class="info-value">{{ $material->delivery_note_number ?: 'غير محدد' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                            </svg>
                            معرف الفاتورة
                        </div>
                        <div class="info-value">{{ $material->purchase_invoice_id ?: 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            المورد
                        </div>
                        <div class="info-value">{{ $material->supplier->name ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            حالة التفعيل
                        </div>
                        <div class="info-value">
                            @if ($material->status == 'available')
                                <span class="status active">متوفر</span>
                            @elseif ($material->status == 'in_use')
                                <span class="status active">قيد الاستخدام</span>
                            @elseif ($material->status == 'consumed')
                                <span class="status inactive">مستهلك</span>
                            @elseif ($material->status == 'expired')
                                <span class="status inactive">منتهي الصلاحية</span>
                            @else
                                <span class="status inactive">{{ $material->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                            تاريخ الإنشاء
                        </div>
                        <div class="info-value">{{ $material->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            تاريخ التحديث
                        </div>
                        <div class="info-value">{{ $material->updated_at->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            &nbsp;
                        </div>
                        <div class="info-value">&nbsp;</div>
                    </div>
                </div>
            </div>

            @if ($course->image_url)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <path d="M21 15l-5-5L5 21"></path>
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
                            <div class="info-value">{{ $material->supplier->contact_person ?? 'غير محدد' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">الهاتف:</div>
                            <div class="info-value">{{ $material->supplier->phone ?? 'غير محدد' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">البريد الإلكتروني:</div>
                            <div class="info-value">{{ $material->supplier->email ?? 'غير محدد' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">العنوان:</div>
                            <div class="info-value">{{ $material->supplier->address ?? 'غير محدد' }}</div>
                        </div>
                        @else
                        <div class="info-value">لا توجد معلومات عن المورد</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        @if ($course->schedules->count() > 0)
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">المواد المرتبطة</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-grid">
                        <div class="info-item">
                            <div class="info-label">عدد الاستاندات المنتجة:</div>
                            <div class="info-value">{{ $material->stage1Stands->count() }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">إجمالي الوزن المستخدم:</div>
                            <div class="info-value">{{ $material->stage1Stands->sum('weight') }} {{ $material->unit }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">إجمالي الهدر:</div>
                            <div class="info-value">{{ $material->stage1Stands->sum('waste') }} {{ $material->unit }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                    <button type="button" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>تعديل المادة</h4>
                            <p>تعديل تفاصيل المادة</p>
                        </div>
                    </button>

                    <form method="POST" action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}" class="delete-form" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>حذف المادة</h4>
                                <p>حذف نهائي للمادة من النظام</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm(
                            '⚠️ هل أنت متأكد من حذف هذه المادة؟\n\nهذا الإجراء لا يمكن التراجع عنه!'
                        )) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
