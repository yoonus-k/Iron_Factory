@extends('master')

@section('title', 'تسجيل البضاعة - المستودع')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <h1 class="page-title">
                        <i class="fas fa-box"></i> تسجيل البضاعة في المستودع
                    </h1>
                    <p class="text-muted mb-0">إدارة تسجيل الشحنات الواردة والتحكم في حركتها</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ خطأ!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="stats-row">
                    <div class="stat-item pending-stat">
                        <div class="stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">🔴 شحنات معلقة (بانتظار التسجيل)</span>
                            <span class="stat-number" style="color: #e74c3c;">{{ $incomingUnregistered->total() ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="stat-item registered-stat">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">🟢 شحنات مسجلة (مكتملة)</span>
                            <span class="stat-number" style="color: #27ae60;">{{ $incomingRegistered->total() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unregistered Shipments -->

    <div class="row">
        <!-- Unregistered Shipments Column -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0" style="color: white;">🔴 شحنات معلقة (تحتاج تسجيل)</h3>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white text-danger" style="font-size: 14px; padding: 6px 12px;">{{ $incomingUnregistered->total() ?? 0 }} شحنة</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($incomingUnregistered->count() > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-arrow-down"></i> <strong>الخطوة 1:</strong> اختر شحنة من القائمة أدناه
                        </div>

                        <div class="operations-timeline">
                            @foreach ($incomingUnregistered as $index => $shipment)
                                <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                    @if($index === count($incomingUnregistered) - 1)
                                        <style>
                                            .operation-item:last-child { border-bottom: none; }
                                        </style>
                                    @endif

                                    <!-- رأس العملية -->
                                    <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                        <div style="flex: 1;">
                                            <!-- النوع والوصف -->
                                            <div class="operation-description" style="margin-bottom: 8px;">
                                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                    <!-- Badge للحالة -->
                                                    <span class="badge" style="background-color: #f39c12; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        معلقة
                                                    </span>

                                                    <!-- تحذير التكرار إن وجد -->
                                                    @if ($shipment->registration_attempts > 0)
                                                        <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            ⚠️ محاولة {{ $shipment->registration_attempts + 1 }}
                                                        </span>
                                                    @endif

                                                    <!-- رقم الشحنة -->
                                                    <strong style="color: #2c3e50; font-size: 14px;">
                                                        #{{ $shipment->note_number ?? $shipment->id }}
                                                    </strong>
                                                </div>
                                            </div>

                                            <!-- تفاصيل الشحنة -->
                                            <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                    <span><strong>{{ $shipment->supplier->name ?? 'N/A' }}</strong></span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 16 16 12 12 8"></polyline>
                                                        <polyline points="8 12 12 16 12 8"></polyline>
                                                    </svg>
                                                    <span style="color: #e74c3c; font-weight: 600;">{{ $shipment->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- زر الإجراء -->
                                        <div style="margin-right: 10px;">
                                            <a href="{{ route('manufacturing.warehouse.registration.create', $shipment) }}"
                                                style="background-color: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                                </svg>
                                                تسجيل الآن
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 20px;">
                            {{ $incomingUnregistered->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; color: #27ae60;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 15px; opacity: 0.5;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <p style="margin: 0; font-size: 14px; font-weight: 600;">
                                ✅ لا توجد شحنات معلقة! جميع الشحنات مسجلة بنجاح.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                                اذهب لقسم الشحنات المسجلة لعرض تفاصيل أو نقل للإنتاج
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Registered Shipments Column -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0" style="color: white;">🟢 الشحنات المسجلة</h3>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white text-success" style="font-size: 14px; padding: 6px 12px;">{{ $incomingRegistered->total() ?? 0 }} شحنة</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($incomingRegistered->count() > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-arrow-right"></i> <strong>الخطوة 2:</strong> عرض التفاصيل أو نقل للإنتاج
                        </div>
                            @foreach ($incomingRegistered as $index => $shipment)
                                <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                    @if($index === count($incomingRegistered) - 1)
                                        <style>
                                            .operation-item:last-child { border-bottom: none; }
                                        </style>
                                    @endif

                                    <!-- رأس العملية -->
                                    <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                        <div style="flex: 1;">
                                            <!-- النوع والوصف -->
                                            <div class="operation-description" style="margin-bottom: 8px;">
                                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                    <!-- Badge للحالة -->
                                                    <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                        </svg>
                                                        مسجلة
                                                    </span>

                                                    <!-- معلومات محاولات التسجيل -->
                                                    @if ($shipment->registration_attempts > 0)
                                                        <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            ℹ️ {{ $shipment->registration_attempts }} محاولة
                                                        </span>
                                                    @endif

                                                    <!-- رقم الشحنة -->
                                                    <strong style="color: #2c3e50; font-size: 14px;">
                                                        #{{ $shipment->note_number ?? $shipment->id }}
                                                    </strong>

                                                    <!-- الوزن -->
                                                    <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                                        </svg>
                                                        {{ number_format($shipment->actual_weight ?? 0, 2) }} كيلو
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- تفاصيل الشحنة -->
                                            <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                    <span><strong>{{ $shipment->supplier->name ?? 'N/A' }}</strong></span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="8.5" cy="7" r="4"></circle>
                                                        <polyline points="17 11 19 13 23 9"></polyline>
                                                    </svg>
                                                    <span>{{ $shipment->registeredBy->name ?? 'N/A' }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $shipment->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 16 16 12 12 8"></polyline>
                                                        <polyline points="8 12 12 16 12 8"></polyline>
                                                    </svg>
                                                    <span>{{ $shipment->registered_at?->diffForHumans() ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- زر الإجراء -->
                                        <div style="margin-right: 10px;">
                                            <a href="{{ route('manufacturing.warehouse.registration.show', $shipment) }}"
                                                style="background-color: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                عرض
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 20px;">
                            {{ $incomingRegistered->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 15px; opacity: 0.5;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <p style="margin: 0; font-size: 14px;">
                                ℹ️ لا توجد شحنات مسجلة حتى الآن. ابدأ بتسجيل الشحنات من القائمة اليسرى.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Stats Row */
        .stats-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1;
            min-width: 280px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.1);
            border-left: 4px solid #0066cc;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #0066cc;
            flex-shrink: 0;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #0066cc;
            line-height: 1;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
        }

        .border-left-danger {
            border-left: 4px solid #0066cc;
        }

        .border-left-success {
            border-left: 4px solid #0066cc;
        }

        .border-left-info {
            border-left: 4px solid #0066cc;
        }

        .text-danger {
            color: #0066cc !important;
        }

        .text-success {
            color: #0066cc !important;
        }

        .text-info {
            color: #0066cc !important;
        }

        .bg-warning {
            background-color: #e3f2fd !important;
            color: #0066cc !important;
        }

        .badge-danger {
            background-color: #0066cc !important;
            color: white !important;
        }

        .bg-success {
            background-color: #0066cc !important;
            color: white !important;
        }

        .bg-info {
            background-color: #e3f2fd !important;
            color: #0066cc !important;
        }

        .bg-danger {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .btn-primary {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        .btn-primary:hover {
            background-color: #004499;
            border-color: #004499;
        }

        .btn-info {
            background-color: #0066cc;
            border-color: #0066cc;
            color: white;
        }

        .btn-info:hover {
            background-color: #004499;
            border-color: #004499;
            color: white;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 0;
        }
    </style>
@endsection
