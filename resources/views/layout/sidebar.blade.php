<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" class="logo">
    </div>

    <nav class="sidebar-menu">
        <ul>
            <!-- الرئيسية / لوحة التحكم -->
            <li>
                <a href="/dashboard" class="active" data-tooltip="لوحة التحكم">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>لوحة التحكم</span>
                </a>
            </li>

            <!-- المستودع -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="المستودع">
                    <i class="fas fa-warehouse"></i>
                    <span>المستودع</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.warehouse-products.index') }}">
                            <i class="fas fa-box"></i> المواد الخام
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.delivery-notes.index') }}">
                            <i class="fas fa-receipt"></i> أذون التسليم
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.purchase-invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i> فواتير المشتريات
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.suppliers.index') }}">
                            <i class="fas fa-truck"></i> الموردين
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.additives.index') }}">
                            <i class="fas fa-paint-brush"></i> الصبغات والبلاستيك
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الأولى: التقسيم والاستاندات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="التقسيم والاستاندات">
                    <i class="fas fa-cut"></i>
                    <span>المرحلة 1: التقسيم</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage1.index') }}">
                            <i class="fas fa-list"></i> قائمة الاستاندات
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage1.create') }}">
                            <i class="fas fa-plus-circle"></i> إنشاء استاند جديد
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('manufacturing.stage1.barcode-scan') }}">
                            <i class="fas fa-barcode"></i> مسح الباركود
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage1.waste-tracking') }}">
                            <i class="fas fa-trash-alt"></i> تتبع الهدر
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الثانية: المعالجة -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="المعالجة">
                    <i class="fas fa-cogs"></i>
                    <span>المرحلة 2: المعالجة</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage2.index') }}">
                            <i class="fas fa-list"></i> المواد قيد المعالجة
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage2.create') }}">
                            <i class="fas fa-play-circle"></i> بدء معالجة جديدة
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('manufacturing.stage2.complete-processing') }}">
                            <i class="fas fa-check-circle"></i> إنهاء معالجة
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage2.waste-statistics') }}">
                            <i class="fas fa-chart-pie"></i> إحصائيات الهدر
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الثالثة: تصنيع الكويلات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="تصنيع الكويلات">
                    <i class="fas fa-codiepie"></i>
                    <span>المرحلة 3: الكويلات</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage3.index') }}">
                            <i class="fas fa-list"></i> قائمة الكويلات
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage3.create') }}">
                            <i class="fas fa-plus-circle"></i> إنشاء كويل جديد
                        </a>
                    </li>
                      <li>
                        <a href="{{ route('manufacturing.stage3.add-dye-plastic') }}">
                            <i class="fas fa-palette"></i> إضافة صبغة/بلاستيك
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage3.completed-coils') }}">
                            <i class="fas fa-check-circle"></i> كويلات مكتملة
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الرابعة: التعبئة والتغليف -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="التعبئة والتغليف">
                    <i class="fas fa-box-open"></i>
                    <span>المرحلة 4: التغليف</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage4.index') }}">
                            <i class="fas fa-list"></i> الكراتين المعبأة
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage4.create') }}">
                            <i class="fas fa-plus-circle"></i> إنشاء كرتون جديد
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الورديات والعمال -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="الورديات">
                    <i class="fas fa-users"></i>
                    <span>الورديات والعمال</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.index') }}">
                            <i class="fas fa-list"></i> قائمة الورديات
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.create') }}">
                            <i class="fas fa-plus-circle"></i> إضافة وردية جديدة
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.current') }}">
                            <i class="fas fa-clock"></i> الورديات الحالية
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.attendance') }}">
                            <i class="fas fa-user-check"></i> سجل الحضور
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الهدر والجودة -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="الجودة والهدر">
                    <i class="fas fa-shield-alt"></i>
                    <span>الجودة والهدر</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="/manufacturing/quality/waste-report">
                            <i class="fas fa-trash"></i> تقرير الهدر
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/quality-monitoring">
                            <i class="fas fa-check-square"></i> مراقبة الجودة
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/downtime-tracking">
                            <i class="fas fa-exclamation-circle"></i> الأعطال والتوقفات
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/waste-limits">
                            <i class="fas fa-cog"></i> حدود الهدر المسموحة
                        </a>
                    </li>
                </ul>
            </li>

            <!-- التقارير والإحصائيات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="التقارير">
                    <i class="fas fa-chart-bar"></i>
                    <span>التقارير والإحصائيات</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar-day"></i> التقرير اليومي
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar-week"></i> التقرير الأسبوعي
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar"></i> التقرير الشهري
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-chart-line"></i> إحصائيات الإنتاج
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-chart-pie"></i> توزيع الهدر
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الإدارة والموارد البشرية -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="الإدارة">
                    <i class="fas fa-users-cog"></i>
                    <span>الإدارة</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-users"></i> إدارة المستخدمين
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-user-shield"></i> الأدوار والصلاحيات
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-history"></i> سجل الأنشطة
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الإعدادات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="الإعدادات">
                    <i class="fas fa-cog"></i>
                    <span>الإعدادات</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-sliders-h"></i> إعدادات عامة
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calculator"></i> المعادلات والحسابات
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-barcode"></i> إعدادات الباركود
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-bell"></i> الإشعارات والتنبيهات
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle submenu toggle
        const submenuToggles = document.querySelectorAll('.submenu-toggle');

        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.has-submenu');

                // Close other submenus
                document.querySelectorAll('.has-submenu').forEach(item => {
                    if (item !== parent) {
                        item.classList.remove('active');
                    }
                });

                // Toggle current submenu
                parent.classList.toggle('active');
            });
        });
    });
</script>