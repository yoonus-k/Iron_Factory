@extends('master')

@section('title', 'المرحلة الثانية: معالجة الاستاندات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                المرحلة الثانية: معالجة الاستاندات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المرحلة الثانية</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        <div class="um-alert-custom um-alert-success" role="alert" style="display: none;">
            <i class="feather icon-check-circle"></i>
            تم حفظ البيانات بنجاح
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
                    قائمة المعالجات
                </h4>
                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    بدء معالجة جديدة
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث بالاستاند...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="created">تم الإنشاء</option>
                                <option value="in_process">قيد المعالجة</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="التاريخ">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
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
                            <th>الباركود</th>
                            <th>الاستاند</th>
                            <th>المادة</th>
                            <th>وزن الدخول</th>
                            <th>وزن الخروج</th>
                            <th>الهدر</th>
                            <th>الحالة</th>
                            <th>المستخدم</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processed as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 12px;">
                                    {{ $item->barcode }}
                                </code>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $item->stand_number ?? 'غير محدد' }}</span>
                            </td>
                            <td>{{ $item->material_name ?? 'غير محدد' }}</td>
                            <td><strong>{{ number_format($item->input_weight, 2) }}</strong> كجم</td>
                            <td><strong style="color: #27ae60;">{{ number_format($item->output_weight, 2) }}</strong> كجم</td>
                            <td><strong style="color: #e74c3c;">{{ number_format($item->waste, 2) }}</strong> كجم</td>
                            <td>
                                @if($item->status == 'in_progress')
                                    <span class="um-badge um-badge-warning">قيد المعالجة</span>
                                @elseif($item->status == 'completed')
                                    <span class="um-badge um-badge-success">مكتمل</span>
                                @elseif($item->status == 'started')
                                    <span class="um-badge um-badge-info">بدأت</span>
                                @else
                                    <span class="um-badge um-badge-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>{{ $item->created_by_name ?? 'غير محدد' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <button onclick="printBarcode('{{ $item->barcode }}', '{{ $item->stand_number }}', '{{ $item->material_name }}', {{ $item->output_weight }})" 
                                        class="um-btn um-btn-sm um-btn-success" 
                                        title="طباعة الباركود"
                                        style="padding: 6px 12px;">
                                    <i class="feather icon-printer"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                <i class="feather icon-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                <p style="margin-top: 15px;">لا توجد معالجات مسجلة بعد</p>
                                <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                                    <i class="feather icon-plus"></i> بدء معالجة جديدة
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
                            <span class="um-info-label">الباركود:</span>
                            <span class="um-info-value">
                                <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 11px;">
                                    {{ $item->barcode }}
                                </code>
                            </span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المادة:</span>
                            <span class="um-info-value">{{ $item->material_name ?? 'غير محدد' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">وزن الدخول:</span>
                            <span class="um-info-value"><strong>{{ number_format($item->input_weight, 2) }}</strong> كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">وزن الخروج:</span>
                            <span class="um-info-value"><strong style="color: #27ae60;">{{ number_format($item->output_weight, 2) }}</strong> كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الهدر:</span>
                            <span class="um-info-value"><strong style="color: #e74c3c;">{{ number_format($item->waste, 2) }}</strong> كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المستخدم:</span>
                            <span class="um-info-value">{{ $item->created_by_name ?? 'غير محدد' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <button onclick="printBarcode('{{ $item->barcode }}', '{{ $item->stand_number }}', '{{ $item->material_name }}', {{ $item->output_weight }})" 
                                class="um-btn um-btn-success" 
                                style="width: 100%;">
                            <i class="feather icon-printer"></i> طباعة الباركود
                        </button>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                    <i class="feather icon-inbox" style="font-size: 64px; opacity: 0.3;"></i>
                    <p style="margin-top: 15px; font-size: 16px;">لا توجد معالجات مسجلة بعد</p>
                    <a href="{{ route('manufacturing.stage2.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                        <i class="feather icon-plus"></i> بدء معالجة جديدة
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($processed->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $processed->firstItem() }} إلى {{ $processed->lastItem() }} من أصل {{ $processed->total() }} معالجة
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
            printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + standNumber + '</title>');
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
            printWindow.document.write('<div class="title">باركود المرحلة الثانية</div>');
            printWindow.document.write('<div class="stand-number">استاند ' + standNumber + '</div>');
            printWindow.document.write('<svg id="print-barcode"></svg>');
            printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
            printWindow.document.write('<div class="info">');
            printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي:</span><span class="value">' + netWeight + ' كجم</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
            printWindow.document.write('</div></div>');
            printWindow.document.write('<script>');
            printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
            printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
            printWindow.document.write('<\/script></body></html>');
            printWindow.document.close();
        }

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
