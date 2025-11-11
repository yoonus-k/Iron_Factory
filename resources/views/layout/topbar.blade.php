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

        if (lang === 'ar') {
            document.documentElement.dir = 'rtl';
            document.documentElement.lang = 'ar';
        } else {
            document.documentElement.dir = 'ltr';
            document.documentElement.lang = 'en';
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
            document.documentElement.dir = 'ltr';
            document.documentElement.lang = 'en';
        } else {
            document.documentElement.dir = 'rtl';
            document.documentElement.lang = 'ar';
        }

        // تمييز اللغة المختارة
        document.querySelectorAll('.language-menu a').forEach(link => {
            if (link.getAttribute('data-lang') === savedLanguage) {
                link.classList.add('active');
            }
        });
    });
</script>

<style>
    /* Language Switcher Styles */
    .language-switcher {
        position: relative;
    }

    .language-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 11px;
        border: none;
        background: linear-gradient(135deg, #f8f9fc, #e9ecef);
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        color: var(--accent-color);
        transition: all 0.3s;
    }

    .language-btn:hover {
        background: linear-gradient(135deg, var(--hawiya-teal-light), var(--hawiya-teal));
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .language-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 150px;
        z-index: 1000;
        animation: slideDown 0.2s ease;
        overflow: hidden;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .language-menu a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        color: #333;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .language-menu a:last-child {
        border-bottom: none;
    }

    .language-menu a:hover {
        background-color: #f5f9ff;
        color: var(--hawiya-teal);
    }

    .language-menu a i {
        font-size: 12px;
        color: #ddd;
        width: 16px;
        text-align: center;
    }

    .language-menu a.active {
        background-color: #f0f7ff;
        color: var(--hawiya-teal);
        font-weight: 600;
    }

    .language-menu a.active i {
        color: var(--hawiya-teal);
    }

    /* RTL Adjustments */
    [dir="rtl"] .language-switcher {
        margin-left: 0;
        margin-right: 0;
    }

    [dir="rtl"] .language-menu {
        right: auto;
        left: 0;
    }

    [dir="rtl"] .language-menu a {
        flex-direction: row-reverse;
    }
</style>
