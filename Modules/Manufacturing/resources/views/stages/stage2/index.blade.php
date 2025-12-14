@extends('master')

@section('title', __('stages.stage2_index_title'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                {{ __('stages.stage2_index_title') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('stages.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('stages.stage2_title') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        <div class="um-alert-custom um-alert-success" role="alert" style="display: none;">
            <i class="feather icon-check-circle"></i>
            {{ __('stages.stand_data_loaded_successfully') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('stages.stage2_processing_list') }}
                    @if(isset($viewingAll) && $viewingAll)
                        <span style="display: inline-block; background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-eye"></i> {{ __('stages.all_operations') }}
                        </span>
                    @else
                        <span style="display: inline-block; background: #10b981; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-user"></i> {{ __('stages.my_operations_only') }}
                        </span>
                    @endif
                </h4>
                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('stages.stage2_start_new_processing') }}
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('stages.search_by_stand') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('stages.all_statuses') }}</option>
                                <option value="created">{{ __('stages.status_created') }}</option>
                                <option value="in_process">{{ __('stages.status_in_process') }}</option>
                                <option value="completed">{{ __('stages.status_completed') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="{{ __('stages.date_table_header') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('stages.search_button') }}
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('stages.reset_button') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('stages.barcode_table_header') }}</th>
                            <th>{{ __('stages.stand_table_header') }}</th>
                            <th>{{ __('stages.material_table_header') }}</th>
                            <th>{{ __('stages.input_weight_table_header') }}</th>
                            <th>{{ __('stages.output_weight_table_header') }}</th>
                            <th>{{ __('stages.waste_table_header') }}</th>
                            <th>{{ __('stages.status_table_header') }}</th>
                            <th>{{ __('stages.user_table_header') }}</th>
                            <th>{{ __('stages.date_table_header') }}</th>
                            <th>{{ __('stages.actions_table_header') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processed as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-weight: 600;">
                                    {{ $item->barcode }}
                                </code>
                            </td>
                            <td><strong>{{ $item->stand_number ?? __('stages.not_specified') }}</strong></td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">{{ $item->material_name ?? __('stages.not_specified') }}</h6>
                                    <p class="um-course-desc">{{ $item->parent_barcode }}</p>
                                </div>
                            </td>
                            <td>{{ number_format($item->input_weight, 2) }} {{ __('stages.kg_unit') }}</td>
                            <td><strong style="color: #27ae60;">{{ number_format($item->output_weight, 2) }} {{ __('stages.kg_unit') }}</strong></td>
                            <td>
                                @if($item->waste > 0)
                                <span class="um-badge um-badge-danger">{{ number_format($item->waste, 2) }} {{ __('stages.kg_unit') }}</span>
                                @else
                                <span class="um-badge um-badge-success">0 {{ __('stages.kg_unit') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'in_progress')
                                    <span class="um-badge um-badge-warning">{{ __('stages.status_in_process') }}</span>
                                @elseif($item->status == 'completed')
                                    <span class="um-badge um-badge-success">{{ __('stages.status_completed') }}</span>
                                @elseif($item->status == 'started')
                                    <span class="um-badge um-badge-info">{{ __('stages.status_created') }}</span>
                                @else
                                    <span class="um-badge um-badge-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>{{ $item->created_by_name ?? 'غير محدد' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('stages.actions_table_header') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <button type="button" class="um-dropdown-item" onclick="printBarcode('{{ $item->barcode }}', '{{ $item->stand_number }}', '{{ $item->material_name }}', {{ $item->output_weight }})" style="color: #27ae60;">
                                            <i class="feather icon-printer"></i>
                                            <span>{{ __('stages.print_barcode_button') }}</span>
                                        </button>
                                        <a href="{{ route('manufacturing.stage2.show', $item->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('stages.view_details') }}</span>
                                        </a>
                                        @if($item->created_by_name)
                                        <div class="um-dropdown-item" style="pointer-events: none; opacity: 0.7;">
                                            <i class="feather icon-user"></i>
                                            <span>{{ $item->created_by_name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                <i class="feather icon-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                <p style="margin-top: 15px;">{{ __('stages.no_processings_recorded') }}</p>
                                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                                    <i class="feather icon-plus"></i> {{ __('stages.stage2_start_new_processing') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($processed as $item)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $item->stand_number ?? 'غير محدد' }}</h6>
                                <span class="um-category-id">#{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        @if($item->status == 'in_progress')
                            <span class="um-badge um-badge-warning">قيد المعالجة</span>
                        @elseif($item->status == 'completed')
                            <span class="um-badge um-badge-success">مكتمل</span>
                        @elseif($item->status == 'started')
                            <span class="um-badge um-badge-info">بدأت</span>
                        @else
                            <span class="um-badge um-badge-secondary">{{ $item->status }}</span>
                        @endif
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.barcode_table_header') }}:</span>
                            <span class="um-info-value">
                                <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 11px;">
                                    {{ $item->barcode }}
                                </code>
                            </span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.material_table_header') }}:</span>
                            <span class="um-info-value">{{ $item->material_name ?? __('stages.not_specified') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.input_weight_table_header') }}:</span>
                            <span class="um-info-value"><strong>{{ number_format($item->input_weight, 2) }}</strong> {{ __('stages.kg_unit') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.output_weight_table_header') }}:</span>
                            <span class="um-info-value"><strong style="color: #27ae60;">{{ number_format($item->output_weight, 2) }}</strong> {{ __('stages.kg_unit') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.waste_table_header') }}:</span>
                            <span class="um-info-value"><strong style="color: #e74c3c;">{{ number_format($item->waste, 2) }}</strong> {{ __('stages.kg_unit') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.user_table_header') }}:</span>
                            <span class="um-info-value">{{ $item->created_by_name ?? __('stages.not_specified') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('stages.date_table_header') }}:</span>
                            <span class="um-info-value">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer" style="display: flex; gap: 8px;">
                        <button onclick="printBarcode('{{ $item->barcode }}', '{{ $item->stand_number }}', '{{ $item->material_name }}', {{ $item->output_weight }})"
                                class="um-btn um-btn-success"
                                style="flex: 1;">
                            <i class="feather icon-printer"></i> طباعة
                        </button>
                        <a href="{{ route('manufacturing.stage2.show', $item->id) }}"
                           class="um-btn um-btn-primary"
                           style="flex: 1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="feather icon-eye"></i> التفاصيل
                        </a>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                    <i class="feather icon-inbox" style="font-size: 64px; opacity: 0.3;"></i>
                    <p style="margin-top: 15px; font-size: 16px;">{{ __('stages.no_processings_recorded') }}</p>
                    <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                        <i class="feather icon-plus"></i> {{ __('stages.stage2_start_new_processing') }}
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($processed->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('stages.stage2_showing_info', ['from' => $processed->firstItem(), 'to' => $processed->lastItem(), 'total' => $processed->total()]) }}
                    </p>
                </div>
                <div>
                    {{ $processed->links() }}
                </div>
            </div>
            @endif
        </section>
    </div>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        function printBarcode(barcode, standNumber, materialName, netWeight) {
            const printWindow = window.open('', '', 'height=650,width=850');
            printWindow.document.write('<html dir="rtl"><head><title>{{ __("stages.print_barcode_action") }} - ' + standNumber + '</title>');
            printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
            printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
            printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #3498db; }');
            printWindow.document.write('.stand-number { font-size: 24px; color: #3498db; font-weight: bold; margin: 20px 0; }');
            printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
            printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
            printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
            printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
            printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
            printWindow.document.write('@media print { body { background: white; } }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<div class="barcode-container">');
            printWindow.document.write('<div class="title">{{ __("stages.stage2_barcode_title") }}</div>');
            printWindow.document.write('<div class="stand-number">{{ __("stages.stand_number_label") }} ' + standNumber + '</div>');
            printWindow.document.write('<svg id="print-barcode"></svg>');
            printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
            printWindow.document.write('<div class="info">');
            printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.material_label") }}</span><span class="value">' + materialName + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.net_weight_label") }}</span><span class="value">' + netWeight + ' {{ __("stages.kg_unit") }}</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.date_label_print") }}</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
            printWindow.document.write('</div></div>');
            printWindow.document.write('<script>');
            printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
            printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
            printWindow.document.write('<\/script></body></html>');
            printWindow.document.close();
        }

        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(d => {
                if (d !== dropdown) d.style.display = 'none';
            });
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.style.display = 'none');
            }
        });

        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.background = '#f8f9fa';
            });
            item.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
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
