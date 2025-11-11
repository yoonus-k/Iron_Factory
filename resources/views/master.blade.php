<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'لوحة التحكم' }}</title>
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


    <!-- Custom CSS -->
    @stack('styles')
</head>

<body class="lang-ar">
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

    <!-- Dashboard JS -->
    <script>
        // تحديد حالة الشاشة
        function isMobile() {
            return window.innerWidth <= 768;
        }

        // دالة تبديل قائمة المستخدم
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            const isVisible = userMenu.style.display === 'block';

            if (isVisible) {
                userMenu.style.display = 'none';
                userMenu.classList.remove('show');
            } else {
                userMenu.style.display = 'block';
                setTimeout(() => {
                    userMenu.classList.add('show');
                }, 10);
            }
        }

        // إغلاق قائمة المستخدم عند النقر خارجها
        document.addEventListener('click', function(event) {
            const userProfile = document.querySelector('.user-profile');
            const userMenu = document.getElementById('userMenu');

            if (!userProfile.contains(event.target)) {
                userMenu.style.display = 'none';
                userMenu.classList.remove('show');
            }
        });

        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileOverlay = document.getElementById('mobileOverlay');

            if (isMobile()) {
                // للشاشات الصغيرة
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');

                // منع التمرير في الخلفية عند فتح القائمة
                if (sidebar.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            } else {
                // للشاشات الكبيرة
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // إغلاق الشريط الجانبي عند النقر على الـ overlay
        document.getElementById('mobileOverlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');

            sidebar.classList.remove('show');
            mobileOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });

        // إغلاق الشريط الجانبي عند الضغط على Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (isMobile()) {
                    const sidebar = document.getElementById('sidebar');
                    const mobileOverlay = document.getElementById('mobileOverlay');

                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        mobileOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }

                // إغلاق قائمة المستخدم أيضاً
                const userMenu = document.getElementById('userMenu');
                userMenu.style.display = 'none';
                userMenu.classList.remove('show');
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
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                // إزالة classes الخاصة بالديسكتوب
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }

            // إغلاق قائمة المستخدم
            userMenu.style.display = 'none';
            userMenu.classList.remove('show');
        });

        // Auto-refresh statistics every 30 seconds
        setInterval(function() {
            // يمكن إضافة كود لتحديث الإحصائيات تلقائياً
        }, 30000);
    </script>

    @stack('scripts')
</body>

</html>
