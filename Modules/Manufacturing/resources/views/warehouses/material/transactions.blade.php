@extends('master')

@section('title', 'حركات المستودع - ' . $material->material_type)

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-activity"></i>
                    </div>
                    <div class="header-info">
                        <h1>سجل حركات المستودع</h1>
                        <p>{{ $material->material_type }} ({{ $material->material_type_en }})</p>
                        <div class="badges">
                            <span class="badge badge-info">إجمالي الحركات: {{ $transactions->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-products.show', $material->id) }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        العودة للمادة
                    </a>
                </div>
            </div>
        </div>

        @if($transactions->count() > 0)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="card-title">سجل الحركات</h3>
                </div>
                <div class="card-body">
                    <div class="um-table-responsive">
                        <table class="um-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم العملية</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>الوحدة</th>
                                    <th>المرجع</th>
                                    <th>التاريخ</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $key => $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $transaction->transaction_number }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $this->getTransactionBadgeClass($transaction->transaction_type) }}">
                                            {{ $this->getTransactionTypeName($transaction->transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold">{{ $transaction->quantity }}</td>
                                    <td>{{ $transaction->unit->name ?? $transaction->unit->name_en ?? 'N/A' }}</td>
                                    <td>{{ $transaction->reference_number ?? '-' }}</td>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <small>{{ $transaction->notes ?? $transaction->notes_en ?? '-' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-top: 20px;">
                @php
                    $receiveTotal = $transactions->where('transaction_type', 'receive')->sum('quantity');
                    $issueTotal = $transactions->where('transaction_type', 'issue')->sum('quantity');
                    $transferTotal = $transactions->where('transaction_type', 'transfer')->sum('quantity');
                    $adjustmentTotal = $transactions->where('transaction_type', 'adjustment')->sum('quantity');
                @endphp

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </div>
                        <h3 class="card-title">الاستقبالات</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="stat-value" style="font-size: 28px; color: #28a745; font-weight: bold;">
                            {{ $receiveTotal }}
                        </div>
                        <div class="stat-label">إجمالي المستقبل</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </div>
                        <h3 class="card-title">الصرف</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="stat-value" style="font-size: 28px; color: #dc3545; font-weight: bold;">
                            {{ $issueTotal }}
                        </div>
                        <div class="stat-label">إجمالي المصروف</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                        <h3 class="card-title">النقل</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="stat-value" style="font-size: 28px; color: #ffc107; font-weight: bold;">
                            {{ $transferTotal }}
                        </div>
                        <div class="stat-label">إجمالي المنقول</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"/>
                                <circle cx="19" cy="12" r="1"/>
                                <circle cx="5" cy="12" r="1"/>
                            </svg>
                        </div>
                        <h3 class="card-title">التسويات</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="stat-value" style="font-size: 28px; color: #17a2b8; font-weight: bold;">
                            {{ $adjustmentTotal }}
                        </div>
                        <div class="stat-label">إجمالي التسويات</div>
                    </div>
                </div>
            </div>

        @else
            <div class="card">
                <div class="card-body text-center" style="padding: 40px;">
                    <div style="font-size: 48px; margin-bottom: 20px;">📭</div>
                    <h3>لا توجد حركات مسجلة</h3>
                    <p style="color: #666; margin-top: 10px;">لم يتم تسجيل أي حركات لهذه المادة حتى الآن</p>
                </div>
            </div>
        @endif
    </div>

    <style>
        .stat-value {
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .um-table {
            font-size: 14px;
        }
        .um-table td {
            vertical-align: middle;
        }
    </style>
@endsection
