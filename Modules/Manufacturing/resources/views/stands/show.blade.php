@extends('master')

@section('title', 'تفاصيل الاستاند')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                تفاصيل الاستاند
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <a href="{{ route('manufacturing.stands.index') }}">الاستاندات</a>
                <i class="feather icon-chevron-left"></i>
                <span>{{ $stand->stand_number }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-info"></i>
                    معلومات الاستاند
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-primary">
                        <i class="feather icon-edit-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i>
                        رجوع
                    </a>
                </div>
            </div>

            <!-- Stand Details -->
            <div class="um-details-section">
                <div class="um-row">
                    <!-- المعلومات الأساسية -->
                    <div class="um-col-md-6">
                        <div class="um-info-card">
                            <h5 class="um-info-card-title">
                                <i class="feather icon-file-text"></i>
                                المعلومات الأساسية
                            </h5>
                            <div class="um-info-list">
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-hash"></i>
                                        رقم الاستاند:
                                    </span>
                                    <span class="um-info-value">
                                        <strong>{{ $stand->stand_number }}</strong>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-activity"></i>
                                        الوزن:
                                    </span>
                                    <span class="um-info-value">
                                        <strong>{{ number_format($stand->weight, 2) }} كجم</strong>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-flag"></i>
                                        المرحلة الحالية:
                                    </span>
                                    <span class="um-info-value">
                                        <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-toggle-{{ $stand->is_active ? 'right' : 'left' }}"></i>
                                        الحالة:
                                    </span>
                                    <span class="um-info-value">
                                        @if($stand->is_active)
                                            <span class="um-badge um-badge-success">نشط</span>
                                        @else
                                            <span class="um-badge um-badge-secondary">غير نشط</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التواريخ والإجراءات -->
                    <div class="um-col-md-6">
                        <div class="um-info-card">
                            <h5 class="um-info-card-title">
                                <i class="feather icon-clock"></i>
                                التواريخ
                            </h5>
                            <div class="um-info-list">
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-calendar"></i>
                                        تاريخ الإنشاء:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->created_at->format('Y-m-d') }}
                                        <small style="color: #999;">({{ $stand->created_at->format('h:i A') }})</small>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-edit"></i>
                                        آخر تحديث:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->updated_at->format('Y-m-d') }}
                                        <small style="color: #999;">({{ $stand->updated_at->format('h:i A') }})</small>
                                    </span>
                                </div>
                                <div class="um-info-item">
                                    <span class="um-info-label">
                                        <i class="feather icon-users"></i>
                                        منذ:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stand->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الملاحظات -->
                @if($stand->notes)
                    <div class="um-row" style="margin-top: 20px;">
                        <div class="um-col-md-12">
                            <div class="um-info-card">
                                <h5 class="um-info-card-title">
                                    <i class="feather icon-message-square"></i>
                                    الملاحظات
                                </h5>
                                <div class="um-notes-content">
                                    {{ $stand->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="um-form-actions">
                <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-primary">
                    <i class="feather icon-edit-2"></i>
                    تعديل الاستاند
                </a>
                
                <form action="{{ route('manufacturing.stands.toggle-status', $stand->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="um-btn um-btn-{{ $stand->is_active ? 'warning' : 'success' }}">
                        <i class="feather icon-{{ $stand->is_active ? 'pause' : 'play' }}-circle"></i>
                        {{ $stand->is_active ? 'تعطيل' : 'تفعيل' }} الاستاند
                    </button>
                </form>

                <form method="POST" action="{{ route('manufacturing.stands.destroy', $stand->id) }}" style="display: inline;" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="um-btn um-btn-danger">
                        <i class="feather icon-trash-2"></i>
                        حذف الاستاند
                    </button>
                </form>
            </div>
        </section>
    </div>

    <style>
        .um-details-section {
            padding: 20px;
        }

        .um-info-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
        }

        .um-info-card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .um-info-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .um-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .um-info-item:hover {
            background: #f0f0f0;
        }

        .um-info-label {
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .um-info-value {
            font-weight: 500;
            color: #333;
            text-align: left;
        }

        .um-notes-content {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            line-height: 1.6;
            color: #555;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {
            .um-row {
                flex-direction: column;
            }

            .um-col-md-6 {
                width: 100%;
                margin-bottom: 15px;
            }

            .um-form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .um-form-actions .um-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من حذف هذا الاستاند؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        this.submit();
                    }
                });
            }

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>

@endsection
