@extends('master')

@section('title', 'عرض فاتورة شراء')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert-custom um-alert-error" role="alert">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="header-info">
                        <h1>فاتورة شراء #{{ $invoice->invoice_number }}</h1>
                        <div class="badges">
                            <span class="badge" style="background-color: #e3f2fd; color: #1976d2;">
                                <i class="feather icon-calendar"></i>
                                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}
                            </span>
                            <span class="badge" style="background-color: {{ $invoice->status->color() == 'warning' ? '#fff3cd' : ($invoice->status->color() == 'success' ? '#d4edda' : ($invoice->status->color() == 'danger' ? '#f8d7da' : '#d1ecf1')) }}; color: {{ $invoice->status->color() == 'warning' ? '#856404' : ($invoice->status->color() == 'success' ? '#155724' : ($invoice->status->color() == 'danger' ? '#721c24' : '#0c5460')) }};">
                                {{ $invoice->status->label() }}
                            </span>
                            @if($invoice->isOverdue())
                                <span class="badge" style="background-color: #f8d7da; color: #721c24;">
                                    <i class="feather icon-alert-triangle"></i>
                                    منتهية الصلاحية
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="position: relative;">
                            <button type="button" class="btn btn-secondary" id="statusBtn" onclick="toggleStatusDropdown()">
                                <i class="feather icon-edit-3"></i>
                                تغيير الحالة
                            </button>
                            <div id="statusDropdown" style="display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 200px;">
                                @php
                                    $statuses = [
                                        'draft' => 'مسودة',
                                        'pending' => 'في الانتظار',
                                        'approved' => 'موافق عليها',
                                        'paid' => 'مدفوعة'
                                    ];
                                @endphp
                                @foreach($statuses as $statusValue => $statusLabel)
                                    <form method="POST" action="{{ route('manufacturing.purchase-invoices.update-status', $invoice->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $statusValue }}">
                                        <button type="submit" style="display: block; width: 100%; padding: 10px 15px; border: none; background: white; text-align: right; cursor: pointer; border-radius: 0; font-size: 14px; color: #333; transition: all 0.3s ease;">
                                            {{ $statusLabel }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="btn btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            تعديل
                        </a>
                        <form method="POST" action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الفاتورة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                حذف
                            </button>
                        </form>
                        <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn btn-back">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="feather icon-inbox"></i>
                    </div>
                    <h3 class="card-title">معلومات الفاتورة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الفاتورة:</div>
                        <div class="info-value">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الرقم المرجعي:</div>
                        <div class="info-value">{{ $invoice->invoice_reference_number ?? 'لا يوجد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            <span class="badge" style="background-color: {{ $invoice->status->color() == 'warning' ? '#fff3cd' : ($invoice->status->color() == 'success' ? '#d4edda' : ($invoice->status->color() == 'danger' ? '#f8d7da' : '#d1ecf1')) }}; color: {{ $invoice->status->color() == 'warning' ? '#856404' : ($invoice->status->color() == 'success' ? '#155724' : ($invoice->status->color() == 'danger' ? '#721c24' : '#0c5460')) }};">
                                {{ $invoice->status->label() }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">النشاط:</div>
                        <div class="info-value">
                            {{ $invoice->is_active ? '✓ نشط' : '✗ غير نشط' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المورد -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="feather icon-user"></i>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المورد:</div>
                        <div class="info-value">{{ $invoice->supplier->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">{{ $invoice->supplier->email ?? 'لا يوجد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">رقم الهاتف:</div>
                        <div class="info-value">{{ $invoice->supplier->phone ?? 'لا يوجد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">العنوان:</div>
                        <div class="info-value">{{ $invoice->supplier->address ?? 'لا يوجد' }}</div>
                    </div>
                </div>
            </div>

            <!-- التواريخ والمبالغ -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <i class="feather icon-calendar"></i>
                    </div>
                    <h3 class="card-title">التواريخ والمبالغ</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">تاريخ الفاتورة:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">تاريخ الاستحقاق:</div>
                        <div class="info-value">
                            @if($invoice->due_date)
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                                @if($invoice->isOverdue())
                                    <span class="badge" style="background-color: #f8d7da; color: #721c24; margin-right: 8px;">
                                        متأخر
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #d4edda; color: #155724; margin-right: 8px;">
                                        {{ $invoice->daysUntilDue() }} يوم متبقي
                                    </span>
                                @endif
                            @else
                                لم يتم تحديد تاريخ
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي:</div>
                        <div class="info-value" style="font-size: 1.2em; font-weight: bold; color: #2c3e50;">
                            {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">شروط الدفع:</div>
                        <div class="info-value">{{ $invoice->payment_terms ?? 'عام' }}</div>
                    </div>
                </div>
            </div>

            <!-- معلومات التسجيل والموافقة -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <i class="feather icon-shield"></i>
                    </div>
                    <h3 class="card-title">معلومات النظام</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">مسجل من قبل:</div>
                        <div class="info-value">{{ $invoice->recordedBy->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">تاريخ التسجيل:</div>
                        <div class="info-value">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($invoice->approvedBy)
                        <div class="info-item">
                            <div class="info-label">وافق عليها:</div>
                            <div class="info-value">{{ $invoice->approvedBy->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">تاريخ الموافقة:</div>
                            <div class="info-value">{{ $invoice->approved_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                    @if($invoice->paid_at)
                        <div class="info-item">
                            <div class="info-label">تاريخ الدفع:</div>
                            <div class="info-value">{{ $invoice->paid_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ملاحظات -->
            @if($invoice->notes)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="feather icon-message-square"></i>
                        </div>
                        <h3 class="card-title">ملاحظات</h3>
                    </div>
                    <div class="card-body">
                        <p style="color: #555; line-height: 1.6; margin: 0;">{{ $invoice->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- سجل العمليات -->
        @if($invoice->operationLogs()->count() > 0)
            <div class="card" style="margin-top: 30px;">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="feather icon-activity"></i>
                    </div>
                    <h3 class="card-title">سجل العمليات</h3>
                </div>
                <div class="card-body">
                    <div style="position: relative; padding: 20px 0;">
                        @foreach($invoice->operationLogs()->orderBy('created_at', 'desc')->get() as $log)
                            <div style="display: flex; margin-bottom: 20px; position: relative;">
                                <div style="min-width: 20px; height: 20px; border-radius: 50%; background-color: #2c3e50; position: relative; left: -10px; top: 5px;"></div>
                                <div style="margin-right: 20px; flex: 1;">
                                    <div style="font-weight: bold; color: #2c3e50; font-size: 14px;">
                                        {{ $log->action_type }}
                                    </div>
                                    <div style="color: #7f8c8d; font-size: 13px; margin-top: 4px;">
                                        {{ $log->user->name ?? 'مستخدم' }} - {{ $log->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    @if($log->description)
                                        <div style="color: #555; font-size: 13px; margin-top: 4px;">
                                            {{ $log->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function toggleStatusDropdown() {
            const dropdown = document.getElementById('statusDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(event) {
            const btn = document.getElementById('statusBtn');
            const dropdown = document.getElementById('statusDropdown');
            if (!btn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
@endsection
