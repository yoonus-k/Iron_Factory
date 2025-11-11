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

    // إغلاق القائمة عند النقر خارجها
    document.addEventListener('click', function(event) {
        const userProfile = document.querySelector('.user-profile');
        const userMenu = document.getElementById('userMenu');

        if (!userProfile.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.style.display = 'none';
        }
    });
</script>
