<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" class="logo">
    </div>

    <nav class="sidebar-menu">
        <ul>
            <!-- الرئيسية / لوحة التحكم -->
            <li>
                <a href="/dashboard" class="active" data-tooltip="{{ __('app.menu.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>{{ __('app.menu.dashboard') }}</span>
                </a>
            </li>


            <!-- المستودع -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.warehouse') }}">
                    <i class="fas fa-warehouse"></i>
                    <span>{{ __('app.menu.warehouse') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.warehouse-products.index') }}">
                            <i class="fas fa-box"></i> {{ __('app.warehouse.raw_materials') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.warehouses.index') }}">
                            <i class="fas fa-box"></i> {{ __('app.warehouse.stores') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.delivery-notes.index') }}">
                            <i class="fas fa-receipt"></i> {{ __('app.warehouse.delivery_notes') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.warehouse.registration.pending') }}">
                            <i class="fas fa-clipboard-check"></i> 📦 {{ __('app.warehouse.registration') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.purchase-invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i> {{ __('app.warehouse.purchase_invoices') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.suppliers.index') }}">
                            <i class="fas fa-truck"></i> {{ __('app.warehouse.suppliers') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('manufacturing.warehouse-settings.index') }}">
                            <i class="fas fa-cog"></i> {{ __('app.menu.settings') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الأولى: التقسيم والاستاندات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage1.title') }}">
                    <i class="fas fa-cut"></i>
                    <span>قائمة الاستاندات</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stands.index') }}">
                            <i class="fas fa-list"></i> قائمة الاستاندات
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage1.create') }}">
                            <i class="fas fa-plus-circle"></i> تقسيم المواد الى استاندات
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('manufacturing.stage1.barcode-scan') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.production.barcode_scan') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage1.waste-tracking') }}">
                            <i class="fas fa-trash-alt"></i> {{ __('app.production.waste_tracking') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الثانية: المعالجة -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage2.title') }}">
                    <i class="fas fa-cogs"></i>
                    <span>{{ __('app.production.stage2.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage2.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage2.list') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage2.create') }}">
                            <i class="fas fa-play-circle"></i> {{ __('app.production.stage2.start_new') }}
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('manufacturing.stage2.complete-processing') }}">
                            <i class="fas fa-check-circle"></i> {{ __('app.production.stage2.complete') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage2.waste-statistics') }}">
                            <i class="fas fa-chart-pie"></i> {{ __('app.production.waste_statistics') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الثالثة: تصنيع الكويلات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage3.title') }}">
                    <i class="fas fa-codiepie"></i>
                    <span>{{ __('app.production.stage3.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage3.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage3.list') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage3.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.production.stage3.create_new') }}
                        </a>
                    </li>
                      <li>
                        <a href="{{ route('manufacturing.stage3.add-dye-plastic') }}">
                            <i class="fas fa-palette"></i> {{ __('app.production.stage3.add_additives') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage3.completed-coils') }}">
                            <i class="fas fa-check-circle"></i> {{ __('app.production.stage3.completed') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- المرحلة الرابعة: التعبئة والتغليف -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage4.title') }}">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('app.production.stage4.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.stage4.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage4.list') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.stage4.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.production.stage4.create_new') }}
                        </a>
                    </li>
                </ul>
            </li>
               <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.production_tracking') }}">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('app.menu.production_tracking') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.production-tracking.scan') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.production.barcode_scan') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.iron-journey') }}">
                            <i class="fas fa-route"></i> {{ __('app.production.iron_journey') }}
                        </a>
                    </li>
                </ul>
            </li>


            <!-- الورديات والعمال -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.shifts') }}">
                    <i class="fas fa-users"></i>
                    <span>{{ __('app.menu.shifts') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.users.shifts_list') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.users.add_shift') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.current') }}">
                            <i class="fas fa-clock"></i> {{ __('app.users.current_shifts') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.attendance') }}">
                            <i class="fas fa-user-check"></i> {{ __('app.users.attendance') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.workers.index') }}">
                            <i class="fas fa-user-tie"></i> إدارة العمال
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.worker-teams.index') }}">
                            <i class="fas fa-users-cog"></i> مجموعات العمال
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الهدر والجودة -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.quality') }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('app.menu.quality') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="/manufacturing/quality/waste-report">
                            <i class="fas fa-trash"></i> {{ __('app.reports.waste_report') }}
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/quality-monitoring">
                            <i class="fas fa-check-square"></i> {{ __('app.production.quality_monitoring') }}
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/downtime-tracking">
                            <i class="fas fa-exclamation-circle"></i> {{ __('app.production.downtime_tracking') }}
                        </a>
                    </li>
                    <li>
                        <a href="/manufacturing/quality/waste-limits">
                            <i class="fas fa-cog"></i> {{ __('app.production.waste_limits') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- التقارير والإحصائيات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.reports') }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>{{ __('app.menu.reports') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar-day"></i> {{ __('app.reports.daily') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar-week"></i> {{ __('app.reports.weekly') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calendar"></i> {{ __('app.reports.monthly') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-chart-line"></i> {{ __('app.reports.production_stats') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-chart-pie"></i> {{ __('app.reports.waste_distribution') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الإدارة والموارد البشرية -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.management') }}">
                    <i class="fas fa-users-cog"></i>
                    <span>{{ __('app.menu.management') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-users"></i> {{ __('app.users.manage_users') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-user-shield"></i> {{ __('app.users.roles') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-history"></i> {{ __('app.users.activity_log') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الإعدادات -->
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.settings') }}">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('app.menu.settings') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="#">
                            <i class="fas fa-sliders-h"></i> {{ __('app.settings.general') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-calculator"></i> {{ __('app.settings.calculations') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.barcode.index') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.settings.barcode_settings') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-bell"></i> {{ __('app.settings.notifications') }}
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
