<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.css">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather/iconfont.css') }}">


    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/reports-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-index.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-add.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/language-switcher.css') }}">


    <!-- Custom CSS -->
    @stack('styles')
</head>

<body class="lang-{{ app()->getLocale() }}">
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        @include('layout.sidebar')

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Bar -->
            @include('layout.topbar')

            <!-- Dashboard Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Language Switcher JS -->
    <script src="{{ asset('assets/js/language-switcher.js') }}"></script>

    <!-- Dashboard JS -->
    <script>
        // تحديد حالة الشاشة
        function isMobile() {
            return window.innerWidth <= 768;
        }

        // الحصول على اتجاه النص الحالي
        function getCurrentDirection() {
            return document.documentElement.getAttribute('dir') || 'rtl';
        }

        // دالة تبديل قائمة المستخدم
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            const userProfile = document.querySelector('.user-profile');

            if (userMenu && userProfile) {
                const isVisible = userMenu.style.display === 'block';
                userMenu.style.display = isVisible ? 'none' : 'block';
            }
        }

        // إغلاق قائمة المستخدم عند النقر خارجها
        document.addEventListener('click', function(event) {
            const userProfile = document.querySelector('.user-profile');
            const userMenu = document.getElementById('userMenu');

            if (userProfile && !userProfile.contains(event.target)) {
                if (userMenu) {
                    userMenu.style.display = 'none';
                    userMenu.classList.remove('show');
                }
            }
        });

        // Sidebar Toggle with RTL/LTR Support
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('mainContent');
                    const mobileOverlay = document.getElementById('mobileOverlay');

                    if (isMobile()) {
                        // للشاشات الصغيرة - إظهار/إخفاء القائمة
                        sidebar.classList.toggle('show');
                        if (mobileOverlay) {
                            mobileOverlay.classList.toggle('show');
                        }

                        // منع التمرير في الخلفية عند فتح القائمة
                        if (sidebar.classList.contains('show')) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                        }
                    } else {
                        // للشاشات الكبيرة - ضغط/توسيع القائمة
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                    }
                });
            }

            // إغلاق الشريط الجانبي عند النقر على الـ overlay
            const mobileOverlay = document.getElementById('mobileOverlay');
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.remove('show');
                    this.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }
        });

        // إغلاق الشريط الجانبي عند الضغط على Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (isMobile()) {
                    const sidebar = document.getElementById('sidebar');
                    const mobileOverlay = document.getElementById('mobileOverlay');

                    if (sidebar && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        if (mobileOverlay) {
                            mobileOverlay.classList.remove('show');
                        }
                        document.body.style.overflow = '';
                    }
                }

                // إغلاق قائمة المستخدم أيضاً
                const userMenu = document.getElementById('userMenu');
                if (userMenu) {
                    userMenu.style.display = 'none';
                    userMenu.classList.remove('show');
                }
            }
        });

        // إعادة تعيين الحالة عند تغيير حجم الشاشة
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const userMenu = document.getElementById('userMenu');

            if (!isMobile()) {
                // إزالة classes الخاصة بالموبايل
                if (sidebar) sidebar.classList.remove('show');
                if (mobileOverlay) mobileOverlay.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                // إزالة classes الخاصة بالديسكتوب
                if (sidebar) sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
            }

            // إغلاق قائمة المستخدم
            if (userMenu) {
                userMenu.style.display = 'none';
                userMenu.classList.remove('show');
            }
        });

        // Auto-refresh statistics every 30 seconds
        setInterval(function() {
            // يمكن إضافة كود لتحديث الإحصائيات تلقائياً
        }, 30000);

        // Close dropdowns when scrolling
        document.addEventListener('scroll', function() {
            document.querySelectorAll('.um-dropdown.active').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }, true);

        // Dropdown functionality
        document.addEventListener('click', function(e) {
            const dropdownButton = e.target.closest('.um-btn-dropdown');
            const dropdown = dropdownButton ? dropdownButton.closest('.um-dropdown') : null;

            // If clicking outside of any dropdown, close all dropdowns
            if (!dropdown) {
                document.querySelectorAll('.um-dropdown.active').forEach(d => {
                    d.classList.remove('active');
                });
            }
        });

        // Prevent closing dropdown when clicking inside
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.um-dropdown-menu').forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Also handle dropdown buttons specifically
            document.querySelectorAll('.um-btn-dropdown').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = this.closest('.um-dropdown');
                    if (dropdown) {
                        // Check if this dropdown is already active
                        const isActive = dropdown.classList.contains('active');

                        // Close all other dropdowns
                        document.querySelectorAll('.um-dropdown.active').forEach(d => {
                            if (d !== dropdown) {
                                d.classList.remove('active');
                            }
                        });

                        // If this dropdown was already active, close it
                        if (isActive) {
                            dropdown.classList.remove('active');
                            return;
                        }

                        // Open this dropdown
                        dropdown.classList.add('active');

                        // Position dropdown menu if it's in a table
                        if (dropdown.closest('.um-table')) {
                            const buttonRect = this.getBoundingClientRect();
                            const menu = dropdown.querySelector('.um-dropdown-menu');
                            if (menu) {
                                // Use fixed positioning to prevent clipping
                                menu.style.position = 'fixed';
                                menu.style.top = 'auto';
                                menu.style.bottom = (window.innerHeight - buttonRect.top + 5) + 'px';
                                menu.style.left = (buttonRect.left - 40) + 'px';
                                menu.style.right = 'auto';
                            }
                        }
                    }
                });

                // Also handle touch events for mobile devices
                button.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = this.closest('.um-dropdown');
                    if (dropdown) {
                        // Check if this dropdown is already active
                        const isActive = dropdown.classList.contains('active');

                        // Close all other dropdowns
                        document.querySelectorAll('.um-dropdown.active').forEach(d => {
                            if (d !== dropdown) {
                                d.classList.remove('active');
                            }
                        });

                        // If this dropdown was already active, close it
                        if (isActive) {
                            dropdown.classList.remove('active');
                            return;
                        }

                        // Open this dropdown
                        dropdown.classList.add('active');

                        // Position dropdown menu if it's in a table
                        if (dropdown.closest('.um-table')) {
                            const buttonRect = this.getBoundingClientRect();
                            const menu = dropdown.querySelector('.um-dropdown-menu');
                            if (menu) {
                                // Use fixed positioning to prevent clipping
                                menu.style.position = 'fixed';
                                menu.style.top = 'auto';
                                menu.style.bottom = (window.innerHeight - buttonRect.top + 5) + 'px';
                                menu.style.left = (buttonRect.left - 40) + 'px';
                                menu.style.right = 'auto';
                            }
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
