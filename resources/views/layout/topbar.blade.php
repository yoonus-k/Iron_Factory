<div class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb">
            <span>لوحة التحكم</span>
        </div>
    </div>

    <div class="topbar-right">
        <!-- الإشعارات -->
        <div class="notification-icon">
            <a href="#">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">5</span>
            </a>
        </div>

        <!-- اللغة -->
        <div class="language-switcher">
            <button class="language-btn" onclick="toggleLanguageMenu()" title="اختر اللغة">
                <i class="fas fa-globe"></i>
            </button>
            <div class="language-menu" id="languageMenu" style="display: none;">
                <a href="#" data-lang="ar" onclick="changeLanguage('ar', event)">
                    <i class="fas fa-check"></i> العربية
                </a>
                <a href="#" data-lang="en" onclick="changeLanguage('en', event)">
                    <i class="fas fa-check"></i> English
                </a>
            </div>
        </div>

        <!-- البحث -->
        <div class="search-box">
            <input type="text" placeholder="البحث..." class="form-control">
            <i class="fas fa-search"></i>
        </div>

        <!-- ملف المستخدم -->
        <div class="user-profile" onclick="toggleUserMenu()">
            <img src="{{ asset('assets/images/avatars/manager.png') }}" alt="الصورة الشخصية" class="user-avatar">
            <div class="user-info">
                <span class="user-name">اسم المستخدم</span>
                <small class="user-role">المدير</small>
            </div>
            <i class="fas fa-chevron-down"></i>
        </div>

        <!-- قائمة المستخدم -->
        <div class="user-menu" id="userMenu" style="display: none;">
            <a href="#"><i class="fas fa-user"></i> الملف الشخصي</a>
            <hr>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
            </a>
        </div>

        <form id="logout-form" action="#" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    function changeLanguage(lang, event) {
        event.preventDefault();

        // حفظ اللغة المختارة
        localStorage.setItem('language', lang);

        // تغيير اللغة والاتجاه
        if (lang === 'ar') {
            document.documentElement.lang = 'ar';
            document.body.classList.remove('lang-en');
            document.body.classList.add('lang-ar');
            document.body.style.direction = 'rtl';
            document.body.style.textAlign = 'right';
        } else {
            document.documentElement.lang = 'en';
            document.body.classList.remove('lang-ar');
            document.body.classList.add('lang-en');
            document.body.style.direction = 'ltr';
            document.body.style.textAlign = 'left';
        }

        // إغلاق القائمة
        document.getElementById('languageMenu').style.display = 'none';

        // تحديث تمييز الخيار المختار
        document.querySelectorAll('.language-menu a').forEach(link => {
            link.classList.remove('active');
        });
        event.target.closest('a').classList.add('active');
    }

    // إغلاق القوائم عند النقر خارجها
    document.addEventListener('click', function(event) {
        const userProfile = document.querySelector('.user-profile');
        const userMenu = document.getElementById('userMenu');
        const languageBtn = document.querySelector('.language-btn');
        const languageMenu = document.getElementById('languageMenu');

        if (userProfile && !userProfile.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.style.display = 'none';
        }

        if (languageBtn && !languageBtn.contains(event.target) && languageMenu && !languageMenu.contains(event.target)) {
            languageMenu.style.display = 'none';
        }
    });

    // تحميل اللغة المحفوظة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const savedLanguage = localStorage.getItem('language') || 'ar';

        if (savedLanguage === 'en') {
            document.documentElement.lang = 'en';
            document.body.classList.remove('lang-ar');
            document.body.classList.add('lang-en');
            document.body.style.direction = 'ltr';
            document.body.style.textAlign = 'left';
        } else {
            document.documentElement.lang = 'ar';
            document.body.classList.remove('lang-en');
            document.body.classList.add('lang-ar');
            document.body.style.direction = 'rtl';
            document.body.style.textAlign = 'right';
        }

        // تمييز اللغة المختارة
        document.querySelectorAll('.language-menu a').forEach(link => {
            if (link.getAttribute('data-lang') === savedLanguage) {
                link.classList.add('active');
            }
        });
    });
</script>

