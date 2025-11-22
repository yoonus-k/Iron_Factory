@extends('master')

@section('title', 'المرحلة الرابعة: التعبئة والتغليف')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
@endsection

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                المرحلة الرابعة: التعبئة والتغليف
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المرحلة الرابعة</span>
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
                    الكراتين المعبأة
                </h4>
                <a href="{{ route('manufacturing.stage4.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إنشاء كرتون جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث برقم الكرتون...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="created">تم الإنشاء</option>
                                <option value="in_process">قيد التعبئة</option>
                                <option value="completed">جاهز للشحن</option>
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
                            <th>رقم الكرتون</th>
                            <th>عدد الكويلات</th>
                            <th>الوزن</th>
                            <th>نوع التغليف</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boxes as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($boxes->currentPage() - 1) * $boxes->perPage() }}</td>
                            <td><span class="badge badge-info">{{ $item->barcode }}</span></td>
                            <td>{{ $item->coils_count }}</td>
                            <td>{{ number_format($item->total_weight, 2) }} كجم</td>
                            <td>{{ $item->packaging_type }}</td>
                            <td>
                                @if($item->status === 'packed')
                                <span class="um-badge um-badge-success">معبأ</span>
                                @elseif($item->status === 'shipped')
                                <span class="um-badge um-badge-info">تم الشحن</span>
                                @else
                                <span class="um-badge um-badge-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>{{ is_string($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : $item->created_at }}</td>
                            <td>
                                <button onclick="printBarcode('{{ $item->barcode }}', '{{ $item->material_name ?? 'غير محدد' }}', {{ $item->total_weight }}, '{{ $item->parent_barcode ?? '' }}')" class="um-btn um-btn-success um-btn-sm" title="طباعة الباركود">
                                    <i class="feather icon-printer"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 50px;">
                                <i class="feather icon-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-2" style="color: #999;">لا توجد كراتين مسجلة</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($boxes as $item)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #e67e2220; color: #e67e22;">
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $item->barcode }}</h6>
                                <span class="um-category-id">#{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        @if($item->status === 'packed')
                        <span class="um-badge um-badge-success">معبأ</span>
                        @elseif($item->status === 'shipped')
                        <span class="um-badge um-badge-info">تم الشحن</span>
                        @else
                        <span class="um-badge um-badge-secondary">{{ $item->status }}</span>
                        @endif
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">عدد الكويلات:</span>
                            <span class="um-info-value">{{ $item->coils_count }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن:</span>
                            <span class="um-info-value">{{ number_format($item->total_weight, 2) }} كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">نوع التغليف:</span>
                            <span class="um-info-value">{{ $item->packaging_type }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">{{ is_string($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : $item->created_at }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <button onclick="printBarcode('{{ $item->barcode }}', '{{ $item->material_name ?? 'غير محدد' }}', {{ $item->total_weight }}, '{{ $item->parent_barcode ?? '' }}')" class="um-btn um-btn-success um-btn-sm">
                            <i class="feather icon-printer"></i>
                            طباعة الباركود
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center" style="padding: 50px;">
                    <i class="feather icon-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-2" style="color: #999;">لا توجد كراتين مسجلة</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($boxes->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $boxes->firstItem() ?? 0 }} إلى {{ $boxes->lastItem() ?? 0 }} من أصل {{ $boxes->total() }} كرتون
                    </p>
                </div>
                <div>
                    {{ $boxes->links() }}
                </div>
            </div>
            @endif
        </section>
    </div>

    <script>
        // Print barcode function
        function printBarcode(barcode, materialName, totalWeight, lafafBarcode) {
            const printWindow = window.open('', '', 'height=650,width=850');
            printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + barcode + '</title>');
            printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
            printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
            printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #e67e22; }');
            printWindow.document.write('.barcode-display { font-size: 24px; color: #e67e22; font-weight: bold; margin: 20px 0; }');
            printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
            printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
            printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
            printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
            printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
            printWindow.document.write('@media print { body { background: white; } }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<div class="barcode-container">');
            printWindow.document.write('<div class="title">باركود المرحلة الرابعة - كرتون</div>');
            printWindow.document.write('<div class="barcode-display">' + barcode + '</div>');
            printWindow.document.write('<svg id="print-barcode"></svg>');
            printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
            printWindow.document.write('<div class="info">');
            printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">الوزن:</span><span class="value">' + totalWeight + ' كجم</span></div>');
            if (lafafBarcode) {
                printWindow.document.write('<div class="info-row"><span class="label">باركود اللفاف:</span><span class="value">' + lafafBarcode + '</span></div>');
            }
            printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
            printWindow.document.write('</div></div>');
            printWindow.document.write('<script>');
            printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
            printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
            printWindow.document.write('<\/script></body></html>');
            printWindow.document.close();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من حذف هذا الكرتون؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الكرتون بنجاح');
                    }
                });
            });

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
