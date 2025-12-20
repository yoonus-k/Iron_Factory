<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" class="logo">
    </div>

    <nav class="sidebar-menu">
        <ul>
            <!-- {{ __('app.menu.dashboard') }} -->
            @if(auth()->user()->hasPermission('MENU_DASHBOARD'))
            <li>
                <a href="" data-tooltip="{{ __('app.menu.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>{{ __('app.menu.dashboard') }}</span>
                </a>
            </li>
            @endif

            <!-- {{ __('app.menu_stage_worker_dashboard') }} -->
            @if(auth()->user()->hasPermission('STAGE_WORKER_DASHBOARD'))
            <li>
                <a href="{{ route('dashboard') }}" data-tooltip="{{ __('app.menu_stage_worker_dashboard') }}">
                    <i class="fas fa-hard-hat"></i>
                    <span>{{ __('app.menu.menu_stage_worker_dashboard') }}</span>
                </a>
            </li>
            
            @endif


            <!-- المستودع -->
            @if(auth()->user()->hasPermission('MENU_WAREHOUSE'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.warehouse') }}">
                    <i class="fas fa-warehouse"></i>
                    <span>{{ __('app.menu.warehouse') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_MATERIALS') || auth()->user()->hasPermission('WAREHOUSE_MATERIALS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.warehouse-products.index') }}">
                            <i class="fas fa-box"></i> {{ __('app.warehouse.raw_materials') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_STAGE1_STANDS') || auth()->user()->hasPermission('STAGE1_STANDS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.stands.index') }}">
                            <i class="fas fa-layer-group"></i> {{ __('app.warehouse.stands') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_STAGE3_COILS') || auth()->user()->hasPermission('STAGE3_COILS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.wrappings.index') }}">
                            <i class="fas fa-tape"></i> اللفافات
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_STORES') || auth()->user()->hasPermission('WAREHOUSE_STORES_READ'))
                    <li>
                        <a href="{{ route('manufacturing.warehouses.index') }}">
                            <i class="fas fa-warehouse"></i> {{ __('app.warehouse.stores') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_DELIVERY_NOTES') || auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_READ'))
                    <li>
                        <a href="{{ route('manufacturing.delivery-notes.index') }}">
                            <i class="fas fa-receipt"></i> {{ __('app.warehouse.delivery_notes') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('manufacturing.coils.transfer-index') }}">
                            <i class="fas fa-exchange-alt"></i> نقل كويلات للإنتاج
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_PURCHASE_INVOICES') || auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_READ'))
                    <li>
                        <a href="{{ route('manufacturing.purchase-invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i> {{ __('app.warehouse.purchase_invoices') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_SUPPLIERS') || auth()->user()->hasPermission('WAREHOUSE_SUPPLIERS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.suppliers.index') }}">
                            <i class="fas fa-truck"></i> {{ __('app.warehouse.suppliers') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_SETTINGS'))
                    <li>
                        <a href="{{ route('manufacturing.warehouse-settings.index') }}">
                            <i class="fas fa-cog"></i> {{ __('app.settings.general') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_WAREHOUSE_REPORTS'))
                    <li>
                        <a href="{{ route('manufacturing.warehouse-reports.index') }}">
                            <i class="fas fa-chart-bar"></i> {{ __('app.menu.reports') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_PRODUCTION_CONFIRMATIONS'))
                    <li>
                        <a href="{{ route('manufacturing.production.confirmations.index') }}">
                            <i class="fas fa-check-circle"></i> {{ __('app.warehouse.production_confirmations') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('WAREHOUSE_INTAKE_READ'))
                    <li>
                        <a href="{{ route('manufacturing.warehouse-intake.index') }}">
                            <i class="fas fa-dolly"></i> {{ __('app.warehouse.intake_requests') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('WAREHOUSE_INTAKE_APPROVE'))
                    <li>
                        <a href="{{ route('manufacturing.warehouse-intake.pending-approval') }}">
                            <i class="fas fa-clipboard-check"></i> {{ __('app.warehouse.approve_intake_requests') }}
                            @php
                                $pendingIntakeCount = \App\Models\WarehouseIntakeRequest::where('status', 'pending')->count();
                            @endphp
                            @if($pendingIntakeCount > 0)
                                <span class="badge badge-danger" style="margin-right: 5px;">{{ $pendingIntakeCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_READ'))
                    <li>
                        <a href="{{ route('manufacturing.finished-product-deliveries.index') }}">
                            <i class="fas fa-truck-loading"></i> {{ __('app.warehouse.product_delivery_notes') }}
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            @endif

            <!-- المرحلة الأولى: الاستاندات -->
            @if(auth()->user()->hasPermission('MENU_STAGE1_STANDS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage1.title') }}">
                    <i class="fas fa-cut"></i>
                    <span>{{ __('app.production.stage1.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <!-- @if(auth()->user()->hasPermission('STAGE1_STANDS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.stands.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage1.list') }}
                        </a>
                    </li>
                    @endif -->

                    @if(auth()->user()->hasPermission('STAGE1_STANDS_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.stage1.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.production.stage1.create_new') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE1_STANDS_READ') || auth()->user()->hasPermission('VIEW_ALL_STAGE1_OPERATIONS'))
                    <li>
                        <a href="{{ route('manufacturing.stage1.index') }}">
                            <i class="fas fa-history"></i>{{ __('app.production.history') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE1_BARCODE_SCAN'))
                    <li>
                        <a href="{{ route('manufacturing.stage1.barcode-scan') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.production.barcode_scan') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE1_WASTE_TRACKING'))
                    <li>
                        <a href="{{ route('manufacturing.stage1.waste-tracking') }}">
                            <i class="fas fa-trash-alt"></i> {{ __('app.production.waste_tracking') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- المرحلة الثانية: المعالجة -->
            @if(auth()->user()->hasPermission('MENU_STAGE2_PROCESSING'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage2.title') }}">
                    <i class="fas fa-cogs"></i>
                    <span>{{ __('app.production.stage2.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('STAGE2_PROCESSING_READ'))
                    <li>
                        <a href="{{ route('manufacturing.stage2.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage2.list') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE2_PROCESSING_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.stage2.create') }}">
                            <i class="fas fa-play-circle"></i> {{ __('app.production.stage2.start_new') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE2_PROCESSING_READ') || auth()->user()->hasPermission('VIEW_ALL_STAGE2_OPERATIONS'))
                    <li>
                        <a href="{{ route('manufacturing.stage2.index') }}">
                            <i class="fas fa-history"></i> {{ __('app.production.history') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE2_COMPLETE_PROCESSING'))
                    <li>
                        <a href="{{ route('manufacturing.stage2.complete-processing') }}">
                            <i class="fas fa-check-circle"></i> {{ __('app.production.stage2.complete') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE2_WASTE_STATISTICS'))
                    <li>
                        <a href="{{ route('manufacturing.stage2.waste-statistics') }}">
                            <i class="fas fa-chart-pie"></i> {{ __('app.production.waste_statistics') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- المرحلة الثالثة: اللفائف -->
            @if(auth()->user()->hasPermission('MENU_STAGE3_COILS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage3.title') }}">
                    <i class="fas fa-codiepie"></i>
                    <span>{{ __('app.production.stage3.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('STAGE3_COILS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.stage3.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage3.list') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE3_COILS_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.stage3.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.production.stage3.create_new') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE3_COILS_READ') || auth()->user()->hasPermission('VIEW_ALL_STAGE3_OPERATIONS'))
                    <li>
                        <a href="{{ route('manufacturing.stage3.index') }}">
                            <i class="fas fa-history"></i> {{ __('app.production.history') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- المرحلة الرابعة: التعبئة -->
            @if(auth()->user()->hasPermission('MENU_STAGE4_PACKAGING'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.production.stage4.title') }}">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('app.production.stage4.title') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('STAGE4_PACKAGING_READ'))
                    <li>
                        <a href="{{ route('manufacturing.stage4.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.production.stage4.list') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE4_PACKAGING_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.stage4.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.production.stage4.create_new') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE4_PACKAGING_READ') || auth()->user()->hasPermission('VIEW_ALL_STAGE4_OPERATIONS'))
                    <li>
                        <a href="{{ route('manufacturing.stage4.index') }}">
                            <i class="fas fa-history"></i> {{ __('app.production.history') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif


            <!-- تأكيدات الإنتاج -->
            @if(auth()->user()->hasPermission('MENU_PRODUCTION_CONFIRMATIONS'))
            <li>
                <a href="{{ route('manufacturing.production.confirmations.index') }}" data-tooltip="تأكيدات الإنتاج">
                    <i class="fas fa-check-circle"></i>
                    <span>تأكيدات الإنتاج</span>
                </a>
            </li>
            @endif

            <!-- التسليم والعملاء -->
            @if(auth()->user()->hasPermission('MENU_FINISHED_PRODUCT_DELIVERIES') ||
                auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE') ||
                auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE') ||
                auth()->user()->hasPermission('MENU_CUSTOMERS') ||
                auth()->user()->hasPermission('STAGE_SUSPENSION_VIEW'))

            <!-- {{ __('app.menu.finished_products') }} -->
            @if(auth()->user()->hasPermission('MENU_FINISHED_PRODUCT_DELIVERIES') || auth()->user()->hasPermission('MENU_CUSTOMERS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.finished_products') }}">
                    <i class="fas fa-truck-loading"></i>
                    <span>{{ __('app.menu.finished_products') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('MENU_FINISHED_PRODUCT_DELIVERIES'))
                    <li>
                        <a href="{{ route('manufacturing.finished-product-deliveries.index') }}">
                            <i class="fas fa-box-open"></i> {{ __('app.finished_products.delivery_notes') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.finished-product-deliveries.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.finished_products.create_delivery_note') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE'))
                    <li>
                        <a href="{{ route('manufacturing.finished-product-deliveries.pending-approval') }}">
                            <i class="fas fa-clock"></i> الإذونات المعلقة
                            @php
                                $pendingDeliveryCount = \App\Models\DeliveryNote::where('status', 'pending')
                                    ->where('type', 'finished_product_outgoing')
                                    ->count();
                            @endphp
                            @if($pendingDeliveryCount > 0)
                                <span class="badge badge-danger" style="margin-right: 5px;">{{ $pendingDeliveryCount }}</span>
                            @endif

                            <i class="fas fa-clock"></i> {{ __('app.finished_products.pending_notes') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_CUSTOMERS'))
                    <li>
                        <a href="{{ route('customers.index') }}">
                            <i class="fas fa-users"></i> {{ __('app.customers.manage') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- تتبع الإنتاج -->
            @if(auth()->user()->hasPermission('MENU_PRODUCTION_TRACKING'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.production_tracking') }}">
                    <i class="fas fa-route"></i>
                    <span>{{ __('app.menu.production_tracking') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('PRODUCTION_TRACKING_SCAN'))
                    <li>
                        <a href="{{ route('manufacturing.production-tracking.scan') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.production.barcode_scan') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('PRODUCTION_IRON_JOURNEY'))
                    <li>
                        <a href="{{ route('manufacturing.iron-journey') }}">
                            <i class="fas fa-route"></i> {{ __('app.production.iron_journey') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- الورديات والعمال -->
            @if(auth()->user()->hasPermission('MENU_SHIFTS_WORKERS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.shifts') }}">
                    <i class="fas fa-users"></i>
                    <span>{{ __('app.menu.shifts') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('SHIFTS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.index') }}">
                            <i class="fas fa-list"></i> {{ __('app.users.shifts_list') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('SHIFTS_CREATE'))
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.create') }}">
                            <i class="fas fa-plus-circle"></i> {{ __('app.users.add_shift') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('SHIFTS_CURRENT'))
                    <li>
                        <a href="{{ route('manufacturing.shifts-workers.current') }}">
                            <i class="fas fa-clock"></i> {{ __('app.users.current_shifts') }}
                        </a>
                    </li>
                    @endif


                    @if(auth()->user()->hasPermission('WORKERS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.workers.index') }}">
                            <i class="fas fa-user-tie"></i> {{ __('app.workers.manage') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('WORKER_TEAMS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.worker-teams.index') }}">
                            <i class="fas fa-users-cog"></i> {{ __('app.workers.teams') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- {{ __('app.menu.quality') }} -->
            @if(auth()->user()->hasPermission('MENU_QUALITY_WASTE'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.quality') }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('app.menu.quality') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('QUALITY_WASTE_REPORT'))
                    <li>
                        <a href="/manufacturing/quality/waste-report">
                            <i class="fas fa-trash"></i> {{ __('app.reports.waste_report') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('QUALITY_MONITORING'))
                    <li>
                        <a href="/manufacturing/quality/quality-monitoring">
                            <i class="fas fa-check-square"></i> {{ __('app.production.quality_monitoring') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('QUALITY_DOWNTIME_TRACKING'))
                    <li>
                        <a href="/manufacturing/quality/downtime-tracking">
                            <i class="fas fa-exclamation-circle"></i> {{ __('app.production.downtime_tracking') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('QUALITY_WASTE_LIMITS'))
                    <li>
                        <a href="/manufacturing/quality/waste-limits">
                            <i class="fas fa-cog"></i> {{ __('app.production.waste_limits') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE_SUSPENSION_VIEW') || auth()->user()->hasPermission('STAGE_SUSPENSION_APPROVE'))
                    <li>
                        <a href="{{ route('stage-suspensions.index') }}">
                            <i class="fas fa-pause-circle"></i> الموافقة على تجاوز الهدر
                            @php
                                $pendingSuspensionsCount = \App\Models\StageSuspension::where('status', 'suspended')->count();
                            @endphp
                            @if($pendingSuspensionsCount > 0)
                                <span class="badge badge-warning" style="margin-right: 5px; background-color: #ff9800;">{{ $pendingSuspensionsCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @endif

            <!-- التقارير الإنتاجية -->
            @if(auth()->user()->hasPermission('MENU_PRODUCTION_REPORTS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.reports') }}">
                    <i class="fas fa-chart-line"></i>
                    <span>{{ __('app.menu.reports') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">

                    @if(auth()->user()->hasPermission('REPORTS_WIP'))
                    <li>
                        <a href="{{ route('manufacturing.reports.wip') }}">
                            <i class="fas fa-hourglass-half"></i> {{ __('app.reports.wip') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('REPORTS_SHIFT_DASHBOARD'))
                    <li>
                        <a href="{{ route('manufacturing.reports.shift-dashboard') }}">
                            <i class="fas fa-clock"></i> {{ __('app.reports.shift_summary') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('REPORTS_STANDS_USAGE'))
                    <li>
                        <a href="{{ route('manufacturing.stands.usage-history') }}">
                            <i class="fas fa-history"></i> {{ __('app.reports.stands_usage_history') }}
                        </a>
                    </li>
                    @endif


                    @if(auth()->user()->hasPermission('REPORTS_WORKER_PERFORMANCE'))
                    <li>
                        <a href="{{ route('manufacturing.reports.worker-performance') }}">
                            <i class="fas fa-user-chart"></i> {{ __('app.reports.worker_performance') }}
                        </a>
                    </li>
                    @endif


                    @if(auth()->user()->hasPermission('STAGE1_STANDS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.reports.stage1-management') }}">
                            <i class="fas fa-toolbox"></i> 📊 تقرير المرحلة الأولى
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE2_PROCESSED_READ'))
                    <li>
                        <a href="{{ route('manufacturing.reports.stage2-management') }}">
                            <i class="fas fa-cogs"></i> ⚙️ تقرير المرحلة الثانية
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE3_COILS_READ'))
                    <li>
                        <a href="{{ route('manufacturing.reports.stage3-management') }}">
                            <i class="fas fa-palette"></i> 🎨 تقرير المرحلة الثالثة
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('STAGE4_PACKAGING_READ'))
                    <li>
                        <a href="{{ route('manufacturing.reports.stage4-management') }}">
                            <i class="fas fa-box-open"></i> 📦 تقرير المرحلة الرابعة
                        </a>
                    </li>
                    @endif


                    <li>
                        <a href="{{ route('manufacturing.reports.product-tracking') }}">
                            <i class="fas fa-barcode"></i> 🔍 تقرير التتبع الشامل
                        </a>
                    </li>

                </ul>
            </li>
            @endif

            <!-- {{ __('app.menu.management') }} -->
            @if(auth()->user()->hasPermission('MENU_MANAGEMENT') || auth()->user()->hasPermission('MENU_MANAGE_USERS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.management') }}">
                    <i class="fas fa-users-cog"></i>
                    <span>{{ __('app.menu.management') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('MENU_MANAGE_USERS') || auth()->user()->hasPermission('MANAGE_USERS_READ'))
                    <li>
                        <a href="{{ route('users.index') }}">
                            <i class="fas fa-users"></i> {{ __('app.users.manage') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_MANAGE_ROLES') || auth()->user()->hasPermission('MANAGE_ROLES_READ'))
                    <li>
                        <a href="{{ route('roles.index') }}">
                            <i class="fas fa-user-shield"></i> {{ __('app.users.roles') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('MENU_MANAGE_PERMISSIONS') || auth()->user()->hasPermission('MANAGE_PERMISSIONS_READ'))
                    <li>
                        <a href="{{ route('permissions.index') }}">
                            <i class="fas fa-key"></i> {{ __('app.permissions.manage') }}
                        </a>
                    </li>
                    @endif

                    <li>
                        <a href="/test-permissions">
                            <i class="fas fa-vial"></i> {{ __('app.permissions.test') }}
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-history"></i> {{ __('app.users.activity_log') }}
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- {{ __('app.users.profile') }} -->
            <li>
                <a href="{{ route('profile') }}" data-tooltip="{{ __('app.users.profile') }}">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ __('app.users.profile') }}</span>
                </a>
            </li>

            <!-- {{ __('app.menu.settings') }} -->
            @if(auth()->user()->hasPermission('MENU_SETTINGS'))
            <li class="has-submenu">
                <a href="javascript:void(0)" class="submenu-toggle" data-tooltip="{{ __('app.menu.settings') }}">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('app.menu.settings') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    @if(auth()->user()->hasPermission('SETTINGS_GENERAL'))
                    <li>
                        <a href="{{ route('settings.index') }}">
                            <i class="fas fa-sliders-h"></i> {{ __('app.settings.general') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('SETTINGS_CALCULATIONS'))
                    <li>
                        <a href="#">
                            <i class="fas fa-calculator"></i> {{ __('app.settings.calculations') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('SETTINGS_BARCODE'))
                    <li>
                        <a href="{{ route('manufacturing.barcode.index') }}">
                            <i class="fas fa-barcode"></i> {{ __('app.settings.barcode_settings') }}
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('SETTINGS_NOTIFICATIONS'))
                    <li>
                        <a href="#">
                            <i class="fas fa-bell"></i> {{ __('app.settings.notifications') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
    </nav>
</div>

<style>
    .submenu-header {
        padding: 8px 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        pointer-events: none;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 5px;
    }

    .submenu-header span {
        display: block;
        padding: 0;
    }

    .dark-mode .submenu-header {
        color: #6b7280;
        border-bottom-color: #374151;
    }

    .sidebar {
        position: fixed;
        top: 0;
        right: 0;
        width: 280px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }

    .sidebar-header .logo {
        max-width: 150px;
        height: auto;
    }

    .sidebar-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu > ul > li {
        border-bottom: 1px solid #f3f4f6;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #374151;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .sidebar-menu a:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    .sidebar-menu a.active {
        background: #3b82f6;
        color: #fff;
    }

    .sidebar-menu a i {
        margin-left: 12px;
        width: 20px;
        text-align: center;
    }

    .sidebar-menu a .arrow {
        margin-right: auto;
        margin-left: 12px;
        transition: transform 0.3s ease;
    }

    .has-submenu.active > a .arrow {
        transform: rotate(180deg);
    }

    .submenu {
        display: none;
        background: #f9fafb;
        padding: 0;
    }

    .has-submenu.active .submenu {
        display: block;
    }

    .submenu li a {
        padding: 10px 20px 10px 52px;
        font-size: 0.9rem;
    }

    .submenu li a:hover {
        background: #e5e7eb;
    }
</style>

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

        // Keep active menu item's parent open
        const activeLink = document.querySelector('.submenu a[href="' + window.location.pathname + '"]');
        if (activeLink) {
            activeLink.classList.add('active');
            const parentSubmenu = activeLink.closest('.has-submenu');
            if (parentSubmenu) {
                parentSubmenu.classList.add('active');
            }
        }
    });
</script>
