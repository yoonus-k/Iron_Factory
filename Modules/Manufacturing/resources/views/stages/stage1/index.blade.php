@extends('master')

@section('title', 'المرحلة الأولى: التقسيم والاستاندات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                المرحلة الأولى: التقسيم والاستاندات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المرحلة الأولى</span>
            </nav>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
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
                    <i class="feather icon-list"></i>
                    قائمة الاستاندات ({{ $stands->total() }})
                    @if(isset($viewingAll) && $viewingAll)
                        <span style="display: inline-block; background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-eye"></i> جميع العمليات
                        </span>
                    @else
                        <span style="display: inline-block; background: #10b981; color: white; padding: 4px 12px; border-radius: 6px; font-size: 13px; margin-right: 10px;">
                            <i class="feather icon-user"></i> عملياتي فقط
                        </span>
                    @endif
                </h4>
                <a href="{{ route('manufacturing.stage1.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    الانتقال للمرحله الاولى
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث بالباركود..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="created" {{ request('status') == 'created' ? 'selected' : '' }}>تم الإنشاء</option>
                                <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="consumed" {{ request('status') == 'consumed' ? 'selected' : '' }}>مستهلك</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" value="{{ request('date') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.stage1.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            @if($stands->count() > 0)
            <!-- Table - Desktop View -->
            <div class="um-table um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الباركود</th>
                            <th>المادة</th>
                            <th>رقم الاستاند</th>
                            <th>الوزن الإجمالي</th>
                            <th>الوزن الصافي</th>
                            <th>الهدر</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stands as $index => $stand)
                        <tr>
                            <td>{{ $stands->firstItem() + $index }}</td>
                            <td>
                                <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-weight: 600;">
                                    {{ $stand->barcode }}
                                </code>
                            </td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">{{ $stand->material_name }}</h6>
                                    <p class="um-course-desc">{{ $stand->parent_barcode }}</p>
                                </div>
                            </td>
                            <td><strong>{{ $stand->stand_number }}</strong></td>
                            <td>{{ number_format($stand->weight, 2) }} كجم</td>
                            <td><strong style="color: #27ae60;">{{ number_format($stand->remaining_weight, 2) }} كجم</strong></td>
                            <td>
                                @if($stand->waste > 0)
                                <span class="um-badge um-badge-danger">{{ number_format($stand->waste, 2) }} كجم</span>
                                @else
                                <span class="um-badge um-badge-success">0 كجم</span>
                                @endif
                            </td>
                            <td>
                                @if($stand->status == 'created')
                                <span class="um-badge um-badge-info">تم الإنشاء</span>
                                @elseif($stand->status == 'in_process')
                                <span class="um-badge um-badge-warning">قيد المعالجة</span>
                                @elseif($stand->status == 'completed')
                                <span class="um-badge um-badge-success">مكتمل</span>
                                @elseif($stand->status == 'consumed')
                                <span class="um-badge um-badge-secondary">مستهلك</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($stand->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <button type="button" class="um-dropdown-item" onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})" style="color: #27ae60;">
                                            <i class="feather icon-printer"></i>
                                            <span>طباعة الباركود</span>
                                        </button>
                                        <a href="{{ route('manufacturing.stage1.show', $stand->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض التفاصيل</span>
                                        </a>
                                        @if($stand->created_by_name)
                                        <div class="um-dropdown-item" style="pointer-events: none; opacity: 0.7;">
                                            <i class="feather icon-user"></i>
                                            <span>{{ $stand->created_by_name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @foreach($stands as $stand)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $stand->stand_number }}</h6>
                                <span class="um-category-id">#{{ $stand->barcode }}</span>
                            </div>
                        </div>
                        @if($stand->status == 'created')
                        <span class="um-badge um-badge-info">تم الإنشاء</span>
                        @elseif($stand->status == 'in_process')
                        <span class="um-badge um-badge-warning">قيد المعالجة</span>
                        @elseif($stand->status == 'completed')
                        <span class="um-badge um-badge-success">مكتمل</span>
                        @elseif($stand->status == 'consumed')
                        <span class="um-badge um-badge-secondary">مستهلك</span>
                        @endif
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">المادة:</span>
                            <span class="um-info-value">{{ $stand->material_name }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن الصافي:</span>
                            <span class="um-info-value">{{ number_format($stand->remaining_weight, 2) }} كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الهدر:</span>
                            <span class="um-info-value">{{ number_format($stand->waste, 2) }} كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">{{ \Carbon\Carbon::parse($stand->created_at)->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <button type="button" class="um-btn um-btn-success" onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})" style="flex: 1;">
                            <i class="feather icon-printer"></i>
                            طباعة
                        </button>
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.stage1.show', $stand->id) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض التفاصيل</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $stands->firstItem() }} إلى {{ $stands->lastItem() }} من أصل {{ $stands->total() }} استاند
                    </p>
                </div>
                <div>
                    {{ $stands->links() }}
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div style="text-align: center; padding: 60px 20px; color: #7f8c8d;">
                <i class="feather icon-package" style="font-size: 64px; opacity: 0.3; margin-bottom: 20px;"></i>
                <h3 style="margin: 0 0 10px 0; color: #2c3e50;">لا توجد استاندات بعد</h3>
                <p style="margin: 0 0 25px 0;">ابدأ بإضافة أول استاند من المرحلة الأولى</p>
                <a href="{{ route('manufacturing.stage1.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة استاند جديد
                </a>
            </div>
            @endif
        </section>
    </div>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        // دالة طباعة الباركود
        function printBarcode(barcode, standNumber, materialName, netWeight) {
            const printWindow = window.open('', '', 'height=650,width=850');
            printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + standNumber + '</title>');
            printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
            printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
            printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }');
            printWindow.document.write('.stand-number { font-size: 24px; color: #667eea; font-weight: bold; margin: 20px 0; }');
            printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
            printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
            printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
            printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
            printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
            printWindow.document.write('@media print { body { background: white; } }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<div class="barcode-container">');
            printWindow.document.write('<div class="title">باركود المرحلة الأولى</div>');
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
            // إخفاء التنبيهات تلقائياً
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
