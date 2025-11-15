@extends('master')

@section('title', 'تفاصيل نوع المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-eye"></i>
                    </div>
                    <div class="header-info">
                        <h1>تفاصيل نوع المادة</h1>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}" class="btn btn-primary">
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
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <!-- بطاقة المعلومات الأساسية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ $materialType->type_name }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            </svg>
                            رمز النوع
                        </div>
                        <div class="info-value">
                            <span class="badge badge-primary">{{ $materialType->type_code }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            </svg>
                            الاسم (عربي)
                        </div>
                        <div class="info-value">{{ $materialType->type_name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            </svg>
                            الاسم (إنجليزي)
                        </div>
                        <div class="info-value">{{ $materialType->type_name_en ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 4l-8 8"></path>
                                <path d="M7 4l8 8"></path>
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            </svg>
                            الفئة
                        </div>
                        <div class="info-value">
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

                    @if ($materialType->default_unit)
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg>
                                الوحدة الافتراضية
                            </div>
                            <div class="info-value">{{ $materialType->unit?->unit_name }} ({{ $materialType->unit?->unit_code }})</div>
                        </div>
                    @endif

                    @if ($materialType->standard_cost)
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                التكلفة القياسية
                            </div>
                            <div class="info-value">{{ $materialType->standard_cost }}</div>
                        </div>
                    @endif

                    @if ($materialType->shelf_life_days)
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                مدة الصلاحية
                            </div>
                            <div class="info-value">{{ $materialType->shelf_life_days }} يوم</div>
                        </div>
                    @endif

                    @if ($materialType->description)
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                الوصف
                            </div>
                            <div class="info-value">{{ $materialType->description }}</div>
                        </div>
                    @endif

                    @if ($materialType->storage_conditions)
                        <div class="info-item">
                            <div class="info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                                شروط التخزين
                            </div>
                            <div class="info-value">{{ $materialType->storage_conditions }}</div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            الحالة
                        </div>
                        <div class="info-value">
                            @if ($materialType->is_active)
                                <span class="badge badge-success">نشط ✓</span>
                            @else
                                <span class="badge badge-secondary">غير نشط</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            أنشئت بواسطة
                        </div>
                        <div class="info-value">{{ $materialType->creator->name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            تاريخ الإنشاء
                        </div>
                        <div class="info-value">{{ $materialType->created_at->format('d-m-Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- بطاقة الإجراءات -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">إجراءات الحذف</h3>
                </div>
                <div class="card-body">
                    <p style="color: #718096; margin-bottom: 20px;">
                        اضغط على زر الحذف أدناه لحذف هذا النوع. هذا الإجراء لا يمكن التراجع عنه.
                    </p>

                    <form method="POST" action="{{ route('manufacturing.warehouse-settings.material-types.destroy', $materialType->id) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                            حذف هذا النوع
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ هل أنت متأكد من حذف هذا النوع؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        this.submit();
                    }
                });
            }
        });
    </script>

@endsection
