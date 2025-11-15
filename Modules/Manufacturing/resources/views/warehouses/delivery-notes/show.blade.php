@extends('master')

@section('title', 'تفاصيل أذن التسليم')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $deliveryNote->note_number }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                {{ $deliveryNote->supplier->getName() }}
                            </span>
                            <span class="badge active">مستلم</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.delivery-notes.edit', $deliveryNote->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-back">
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
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات أذن التسليم</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الأذن:</div>
                        <div class="info-value">{{ $deliveryNote->note_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ التسليم:</div>
                        <div class="info-value">{{ $deliveryNote->delivery_date->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوزن الإجمالي:</div>
                        <div class="info-value">{{ $deliveryNote->delivered_weight }} كيلوغرام</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">اسم السائق:</div>
                        <div class="info-value">{{ $deliveryNote->driver_name ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم المركبة:</div>
                        <div class="info-value">{{ $deliveryNote->vehicle_number ?? 'غير محدد' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الاستقبال</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">استقبل بواسطة:</div>
                        <div class="info-value">{{ $deliveryNote->receiver->name ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء:</div>
                        <div class="info-value">{{ $deliveryNote->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">آخر تحديث:</div>
                        <div class="info-value">{{ $deliveryNote->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <path d="M12 1v6m0 6v6"></path>
                            <path d="M1 12h6m6 0h6"></path>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المورد:</div>
                        <div class="info-value">{{ $deliveryNote->supplier->getName() }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الشخص المسؤول:</div>
                        <div class="info-value">{{ $deliveryNote->supplier->contact_person ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الهاتف:</div>
                        <div class="info-value">{{ $deliveryNote->supplier->phone ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">{{ $deliveryNote->supplier->email ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">العنوان:</div>
                        <div class="info-value">{{ $deliveryNote->supplier->address ?? 'غير محدد' }}</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="21 8 21 21 3 21 3 8"></polyline>
                            <line x1="1" y1="3" x2="23" y2="3"></line>
                            <path d="M10 12v4"></path>
                            <path d="M14 12v4"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المادة:</div>
                        <div class="info-value">{{ $deliveryNote->material->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">نوع المادة:</div>
                        <div class="info-value">{{ $deliveryNote->material->materialType->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الوحدة:</div>
                        <div class="info-value">{{ $deliveryNote->material->unit->name ?? 'غير محدد' }}</div>
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
                    <button type="button" class="action-btn print" onclick="window.print()">
                        <div class="action-icon">
                            <i class="feather icon-printer"></i>
                        </div>
                        <div class="action-text">
                            <span>طباعة</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn download">
                        <div class="action-icon">
                            <i class="feather icon-download"></i>
                        </div>
                        <div class="action-text">
                            <span>تحميل PDF</span>
                        </div>
                    </button>

                    <form action="{{ route('manufacturing.delivery-notes.destroy', $deliveryNote->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete" onclick="return confirm('⚠️ هل أنت متأكد من حذف هذا الأذن؟\n\nهذا الإجراء لا يمكن التراجع عنه!')">
                            <div class="action-icon">
                                <i class="feather icon-trash-2"></i>
                            </div>
                            <div class="action-text">
                                <span>حذف</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const downloadButton = document.querySelector('.action-btn.download');
            if (downloadButton) {
                downloadButton.addEventListener('click', function() {
                    alert('جاري تحميل ملف PDF...');
                });
            }
        });
    </script>
@endsection